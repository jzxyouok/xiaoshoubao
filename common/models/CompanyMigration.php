<?php

namespace common\models;

/**
 * This is the model class for table "company_migration".
 *
 * @property integer $id
 * @property integer $company_id
 * @property string $operation
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class CompanyMigration extends \common\models\base\ActiveRecord
{
    const OPERATION_INSERT = 'insert';
    const OPERATION_UPDATE = 'update';
    const OPERATION_DELETE = 'delete';

    const STATUS_WAIT = 0; // 等待操作
    const STATUS_DOING = 1; // 正在操作中
    const STATUS_ERROR = 2; // 操作失败

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company_migration';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id', 'operation'], 'required'],
            [['company_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['operation'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => '企业编号',
            'operation' => '操作',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * 创建一个记录
     * @param $companyId
     * @param $operation
     */
    public static function createOne($companyId, $operation)
    {
        $model = static::find()->where(['company_id' => $companyId, 'operation' => $operation, 'status' => static::STATUS_WAIT])->one();
        if (!$model) {
            $model = new static();
            $model->company_id = $companyId;
            $model->operation = $operation;
            $model->status = static::STATUS_WAIT;
            $model->created_at = time();
        }
        $model->updated_at = time();
        $model->save(false);
    }
}
