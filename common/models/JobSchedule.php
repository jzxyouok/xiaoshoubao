<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "job_schedule".
 *
 * @property integer $id
 * @property string $job_name
 * @property string $schedule_percent
 * @property string $current_description
 * @property integer $status
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 */
class JobSchedule extends \common\models\base\ActiveRecord
{
    const STATUS_WAIT = 0;
    const STATUS_DOING = 10;
    const STATUS_DONE = 20;
    const STATUS_ERROR = 30;

    const CREATED_BY_SYSTEM = 0;

    public static $statusData = [
        self::STATUS_WAIT => '等待中',
        self::STATUS_DOING => '正在执行',
        self::STATUS_DONE => '已完成',
        self::STATUS_ERROR => '执行出错',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'job_schedule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['job_name'], 'required'],
            [['schedule_percent'], 'number'],
            [['status', 'created_at', 'created_by', 'updated_at'], 'integer'],
            [['job_name', 'current_description'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'job_name' => '任务名称',
            'schedule_percent' => '进度',
            'current_description' => '当前进度描述',
            'status' => '状态',
            'created_at' => '创建时间',
            'created_by' => '创建人',
            'updated_at' => '修改时间',
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->isNewRecord) {
            $this->created_at = time();
            $this->created_by = (PHP_SAPI == 'cli') ? static::CREATED_BY_SYSTEM : Yii::$app->user->id;
        }
        $this->updated_at = time();

        return true;
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        return $this->toName($this->status, static::$statusData);
    }

    /**
     * 创建一个任务
     * @param $jobName
     * @return static
     */
    public static function createOne($jobName)
    {
        $model = new static();
        $model->job_name = $jobName;
        $model->schedule_percent = 0;
        $model->current_description = '等待执行';
        $model->status = static::STATUS_WAIT;
        $model->created_at = time();
        $model->updated_at = time();
        $model->save(false);
        return $model;
    }

    /**
     * 开始
     */
    public function start()
    {
        $this->status = static::STATUS_DOING;
        $this->current_description = '开始执行';
        $this->save(false);
    }

    /**
     * 更新进度
     * @param $current
     * @param $total
     * @param string $description
     */
    public function updatePercent($current, $total, $description = '进行中')
    {
        $this->status = static::STATUS_DOING;
        $this->schedule_percent = $current / $total * 100;
        $this->current_description = $description;
        $this->save(false);
    }

    /**
     * 执行结束
     */
    public function end()
    {
        $this->status = static::STATUS_DONE;
        $this->schedule_percent = 100;
        $this->current_description = '已完成';
        $this->save(false);
    }

    /**
     * 执行出错
     * @param $errorMsg
     */
    public function error($errorMsg)
    {
        $this->status = static::STATUS_ERROR;
        $this->current_description = '错误：' . $errorMsg;
        $this->save(false);
    }
}
