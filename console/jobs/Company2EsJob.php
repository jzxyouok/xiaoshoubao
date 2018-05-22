<?php

namespace console\jobs;

use common\models\Company;
use common\models\CompanyMigration;
use common\models\elasticsearch\Company as EsCompany;
use console\jobs\base\BaseJobSchedule;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\elasticsearch\BulkCommand;
use yii\web\NotFoundHttpException;

/**
 * 将 CompanyMigration 更新记录更新到 ElasticSearch
 */
class Company2EsJob extends BaseJobSchedule
{
    const ES_INSERT = 'index';
    const ES_UPDATE = 'update';
    const ES_DELETE = 'delete';

    const SQL_ES_OPERATION_MAP = [
        CompanyMigration::OPERATION_INSERT => self::ES_INSERT,
        CompanyMigration::OPERATION_UPDATE => self::ES_UPDATE,
        CompanyMigration::OPERATION_DELETE => self::ES_DELETE,
    ];

    /**
     * @inheritdoc
     */
    public function jobExecute($queue)
    {
        $jobSchedule = $this->getJobSchedule();
        $query = CompanyMigration::find()->where(['status' => CompanyMigration::STATUS_WAIT]);
        $bulkCommand = EsCompany::getDb()->createBulkCommand([
            'index' => EsCompany::index(),
            'type' => EsCompany::type(),
        ]);

        $total = (int)(clone $query)->count() / 100;
        foreach ($query->batch(100) as $key => $models) {
            try {
                $this->solveOneBatch($models, clone $bulkCommand);

                $jobSchedule->updatePercent($key, $total, '同步正式数据到ES');
            } catch (Exception $e) {
                $this->logger($e->getMessage(), 'error');
            }
        }
    }

    /**
     * @param $models CompanyMigration[]
     * @param $bulkCommand BulkCommand
     * @throws NotSupportedException
     */
    protected function solveOneBatch($models, $bulkCommand)
    {
        $ids = [];
        $companyIds = [];
        foreach ($models as $model) {
            $ids[] = $model->id;
            $companyIds[] = $model->company_id;
        }
        // 开始迁移之前先将记录全部设成正在处理
        CompanyMigration::updateAll(['status' => CompanyMigration::STATUS_DOING, 'updated_at' => time()], ['id' => $ids]);
        // 构建批量操作的 ES 的 body
        $companies = Company::find()->where(['id' => $companyIds])->indexBy('id')->all();
        foreach ($models as $model) {
            switch ($model->operation) {
                case CompanyMigration::OPERATION_INSERT:
                    if (!isset($companies[$model->company_id])) {
                        throw new NotFoundHttpException('未找到该企业信息:' . $model->company_id);
                    }
                    $bulkCommand->addAction(
                        [static::ES_INSERT => ['_id' => $model->company_id]],
                        EsCompany::getSyncDataAttributes($companies[$model->company_id])
                    );
                    break;
                case CompanyMigration::OPERATION_UPDATE:
                    if (!isset($companies[$model->company_id])) {
                        throw new NotFoundHttpException('未找到该企业信息:' . $model->company_id);
                    }
                    $bulkCommand->addAction(
                        [static::ES_UPDATE => ['_id' => $model->company_id]],
                        ['doc' => EsCompany::getSyncDataAttributes($companies[$model->company_id]), 'doc_as_upsert' => true]
                    );
                    break;
                case CompanyMigration::OPERATION_DELETE:
                    $bulkCommand->addDeleteAction($model->company_id);
                    break;
                default:
                    throw new NotSupportedException('未知的操作类型');
            }
        }
        $response = $bulkCommand->execute();
        // 对响应结果进行处理
        $successIds = [
            static::ES_INSERT => [],
            static::ES_UPDATE => [],
            static::ES_DELETE => [],
        ];
        $errorIds = [
            static::ES_INSERT => [],
            static::ES_UPDATE => [],
            static::ES_DELETE => [],
        ];
        foreach ($response['items'] as $item) {
            $this->solveResponseItem($item, $successIds, $errorIds);
        }
        foreach ($successIds as $esOperation => $companyIds) {
            if ($companyIds) {
                $this->logger($esOperation . ':' . implode(',', $companyIds));
                CompanyMigration::deleteAll([
                    'company_id' => $companyIds,
                    'operation' => $this->getSqlOperation($esOperation)
                ]);
            }
        }
        foreach ($errorIds as $esOperation => $companyIds) {
            if ($companyIds) {
                CompanyMigration::updateAll([
                    'status' => CompanyMigration::STATUS_ERROR,
                    'updated_at' => time()
                ], [
                    'company_id' => $companyIds,
                    'operation' => $this->getSqlOperation($esOperation),
                ]);
            }
        }
    }

    /**
     * @param $esOperation
     * @return mixed
     */
    protected function getSqlOperation($esOperation)
    {
        $arr = [
            static::ES_INSERT => CompanyMigration::OPERATION_INSERT,
            static::ES_UPDATE => CompanyMigration::OPERATION_UPDATE,
            static::ES_DELETE => CompanyMigration::OPERATION_DELETE,
        ];
        return $arr[$esOperation];
    }

    /**
     * @param $item
     * @param $successIds
     * @param $errorIds
     */
    protected function solveResponseItem($item, &$successIds, &$errorIds)
    {
        $operations = [static::ES_INSERT, static::ES_UPDATE, static::ES_DELETE];
        foreach ($operations as $operation) {
            if (isset($item[$operation])) {
                if (isset($item[$operation]['status']) && in_array($item[$operation]['status'], [200, 201])) {
                    $successIds[$operation][] = $item[$operation]['_id'];
                } else {
                    $errorIds[$operation][] = $item[$operation]['_id'];
                    $this->logger(implode(':', [
                        $operation,
                        $item[$operation]['_id'],
                        json_encode($item[$operation], JSON_UNESCAPED_UNICODE)
                    ]), 'error');
                }
                break;
            }
        }
    }
}
