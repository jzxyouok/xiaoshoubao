<?php

namespace console\jobs;

use common\models\CompanyUpload;
use common\models\JobSchedule;
use console\jobs\base\BaseJobSchedule;

/**
 * 从 CompanyUpload 导入到 Company 表，并记录 CompanyMigration 用于迁移到 ES
 */
class CompanyUpload2CompanyJob extends BaseJobSchedule
{
    /**
     * @var int
     */
    public $operateAdminId;

    /**
     * @inheritdoc
     */
    public function jobExecute($queue)
    {
        $jobSchedule = $this->getJobSchedule();
        $query = CompanyUpload::find()->select(['id'])->asArray();
        $total = (int)$query->count() / 1000;
        foreach ($query->batch(2000) as $key => $models) {
            /** @var CompanyUpload[] $models */
            $companyUploadIdFrom = current($models)['id'];
            $companyUploadIdTo = $companyUploadIdFrom;
            $end = end($models);
            if ($end) {
                $companyUploadIdTo = $end['id'];
            }
            if ($companyUploadIdTo > $companyUploadIdFrom) {
                $subJobSchedule = JobSchedule::createOne('同步临时企业信息到正式库-子任务' . ($key + 1));
                $queue->push(new CompanyUpload2CompanySubJob([
                    'jobScheduleId' => $subJobSchedule->id,
                    'operateAdminId' => $this->operateAdminId,
                    'companyUploadIdFrom' => $companyUploadIdFrom,
                    'companyUploadIdTo' => $companyUploadIdTo,
                ]));
            }

            $jobSchedule->updatePercent($key, $total, '分配子任务');
        }
    }

}
