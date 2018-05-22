<?php

namespace console\jobs\base;

use common\models\base\ConfigString;
use common\models\JobSchedule;
use Yii;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\base\InvalidValueException;
use yii\queue\JobInterface;
use yii\web\NotFoundHttpException;

abstract class BaseJobSchedule extends BaseObject implements JobInterface
{
    /**
     * @var int
     */
    public $jobScheduleId;

    /**
     * @var false|JobSchedule
     */
    private $_jobSchedule = false;

    /**
     * @return JobSchedule|false|null|static
     * @throws NotFoundHttpException
     */
    protected function getJobSchedule()
    {
        if (!$this->jobScheduleId) {
            throw new InvalidValueException('必须配置 jobScheduleId');
        }
        if ($this->_jobSchedule === false) {
            $this->_jobSchedule = JobSchedule::findOne($this->jobScheduleId);
        }
        if (!$this->_jobSchedule) {
            throw new NotFoundHttpException('未找到 JobSchedule:' . $this->jobScheduleId);
        }
        return $this->_jobSchedule;
    }

    /**
     * @param \yii\queue\Queue $queue
     * @throws NotFoundHttpException
     */
    public function execute($queue)
    {
        ini_set('memory_limit', '1024M');

        $jobSchedule = $this->getJobSchedule();
        $jobSchedule->start();
        $this->logger('操作开始-' . get_called_class());

        try {
            $this->jobExecute($queue);

            $jobSchedule->end();
        } catch (Exception $e) {
            Yii::error($e);
            $jobSchedule->error($e->getMessage());
        }
        $this->logger('操作结束-' . get_called_class());
    }

    /**
     * @param \yii\queue\Queue $queue
     * @throws Exception
     */
    abstract function jobExecute($queue);

    /**
     * @param $msg
     * @param string $type
     */
    protected function logger($msg, $type = 'info')
    {
        Yii::$type($msg, ConfigString::CATEGORY_COMPANY_MIGRATION);
    }
}