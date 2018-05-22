<?php

namespace common\models;

use kriss\modules\auth\models\AuthRole;
use Yii;

/**
 * This is the model class for table "user_auth_role".
 *
 * @property integer $id
 * @property integer $team_id
 * @property string $name
 * @property string $description
 * @property string $operation_list
 */
class UserAuthRole extends AuthRole
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_auth_role';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['team_id'], 'integer'],
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
            'team_id' => '团队',
            'name' => '角色名称',
            'description' => '描述',
            'operation_list' => '权限列表',
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (!$this->team_id) {
            $this->team_id = 0;
            $user = Yii::$app->user->identity;
            if ($user instanceof User) {
                $this->team_id = $user->team_id;
            }
        }

        return true;
    }

    public static function find()
    {
        $query = parent::find();

        $user = Yii::$app->user->identity;
        if ($user instanceof User) {
            $query->andWhere(['team_id' => $user->team_id]);
        }

        return $query;
    }

    /**
     * @inheritdoc
     */
    public static function canLoginUserModify($roleId)
    {
        /** @var \kriss\modules\auth\components\User $user */
        $user = Yii::$app->user;
        $userIdentity = $user->identity;
        // 获取本团队下的超级管理员的 id
        if ($userIdentity instanceof User && !$user->superAdminId) {
            $user->superAdminId = static::find()->select(['id'])->andWhere(['operation_list' => 'all'])->scalar();
        }
        // 超级管理员角色不能被修改
        if ($roleId == $user->superAdminId) {
            return false;
        }
        // 角色 id 在当前登录的用户的角色内，不能修改
        $authRole = $user->userAuthRoleAttribute;
        $adminAuthRole = $userIdentity->$authRole;
        if (strpos(",$adminAuthRole,", ",$roleId,") !== false) {
            return false;
        }
        return true;
    }

    /**
     * 获取超级管理员角色
     * @param $teamId
     * @return UserAuthRole
     */
    public static function getSuperAdminRole($teamId)
    {
        // 查找是否有超级管理员角色存在
        $userAuthRole = UserAuthRole::find()->andWhere(['team_id' => $teamId, 'operation_list' => 'all'])->one();
        if (!$userAuthRole) {
            $userAuthRole = new UserAuthRole();
            $userAuthRole->team_id = $teamId;
            $userAuthRole->name = '超级管理员';
            $userAuthRole->description = '拥有所有权限';
            $userAuthRole->operation_list = 'all';
            $userAuthRole->save(false);
        }
        return $userAuthRole;
    }
}
