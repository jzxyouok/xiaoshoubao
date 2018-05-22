<?php

namespace console\controllers;

use kriss\modules\auth\console\controllers\InitAuthController;
use yii\base\NotSupportedException;

class InitAuthUserController extends InitAuthController
{
    public $adminClass = 'common\models\User';
    public $superAdminId = '';
    public $authClass = 'common\models\base\UserAuth';
    public $authOperationClass = 'common\models\UserAuthOperation';
    public $authRoleClass = 'common\models\UserAuthRole';

    /**
     * 该方法清除角色信息在用户角色下不可用，否则会导致所有角色信息被删除
     * @inheritdoc
     */
    public function actionRestore()
    {
        throw new NotSupportedException('use update-operations');
    }
}