<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "log_operation".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $user_type
 * @property string $operation
 * @property string $remark
 * @property string $ip
 * @property integer $created_at
 */
class LogOperation extends \common\models\base\ActiveRecord
{
    const USER_TYPE_USER = 1;
    const USER_TYPE_ADMIN = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log_operation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'user_type', 'operation', 'ip'], 'required'],
            [['user_id', 'user_type', 'created_at'], 'integer'],
            [['operation', 'ip'], 'string', 'max' => 255],
            [['remark'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户编号',
            'user_type' => '用户类型',
            'operation' => '操作',
            'remark' => '备注',
            'ip' => 'ip',
            'created_at' => '创建时间',
        ];
    }

    /**
     * 记录用户操作
     * @param $operation
     * @param null $remark
     */
    public static function recordUserOperation($operation, $remark = null)
    {
        $model = new static();
        $model->user_id = Yii::$app->user->isGuest ? 0 : Yii::$app->user->id;
        $model->user_type = static::USER_TYPE_USER;
        $model->operation = $operation;
        $model->remark = json_encode($remark, JSON_UNESCAPED_UNICODE);
        $model->ip = Yii::$app->request->userIP;
        $model->created_at = time();
        $model->save(false);
    }

    /**
     * 记录管理员操作
     * @param $operation
     * @param null $remark
     */
    public static function recordAdminOperation($operation, $remark = null)
    {
        $model = new static();
        $model->user_id = Yii::$app->user->isGuest ? 0 : Yii::$app->user->id;
        $model->user_type = static::USER_TYPE_ADMIN;
        $model->operation = $operation;
        $model->remark = json_encode($remark, JSON_UNESCAPED_UNICODE);
        $model->ip = Yii::$app->request->userIP;
        $model->created_at = time();
        $model->save(false);
    }
}
