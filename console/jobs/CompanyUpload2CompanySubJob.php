<?php

namespace console\jobs;

use common\models\Company;
use common\models\CompanyUpload;
use console\jobs\base\BaseJobSchedule;

class CompanyUpload2CompanySubJob extends BaseJobSchedule
{
    /**
     * @var int
     */
    public $operateAdminId;
    /**
     * @var int
     */
    public $companyUploadIdFrom;
    /**
     * @var int
     */
    public $companyUploadIdTo;

    /**
     * @inheritdoc
     */
    function jobExecute($queue)
    {
        $jobSchedule = $this->getJobSchedule();
        $query = CompanyUpload::find()->where(['between', 'id', $this->companyUploadIdFrom, $this->companyUploadIdTo]);
        $total = (int)$query->count() / 200;
        foreach ($query->batch(200) as $key => $models) {
            $this->solveOneBatch($models);

            $jobSchedule->updatePercent($key, $total, '同步上传数据正式库');
        }
    }

    /**
     * @param $models CompanyUpload[]
     */
    protected function solveOneBatch($models)
    {
        $deleteIds = [];
        foreach ($models as $name => $model) {
            // 按照名称唯一来更新数据
            $company = Company::find()->where(['name' => $model->name])->one();
            if (!$company) {
                $company = new Company();
                $company->created_at = time();
                $company->created_by = $this->operateAdminId;
                $company->status = Company::STATUS_NORMAL;
            }
            $company->attributes = $model->attributes;
            $company->updated_at = time();
            $company->updated_by = $this->operateAdminId;
            $result = $company->save(false);
            if (!$result) {
                $this->logger('更新错误：' . $model->id, 'error');
                $this->logger(json_encode($model->attributes), 'error');
                $this->logger(json_encode($company->attributes), 'error');
            } else {
                $deleteIds[] = $model->id;
            }
        }
        // 删除 Upload 中的数据
        CompanyUpload::deleteAll(['id' => $deleteIds]);
    }
}