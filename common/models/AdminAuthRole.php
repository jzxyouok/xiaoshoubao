<?php

namespace common\models;

use kriss\modules\auth\models\AuthRole;

/**
 * This is the model class for table "admin_auth_role".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $operation_list
 */
class AdminAuthRole extends AuthRole
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_auth_role';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['operation_list'], 'string'],
            [['name', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '角色名称',
            'description' => '描述',
            'operation_list' => '权限列表',
        ];
    }
}
