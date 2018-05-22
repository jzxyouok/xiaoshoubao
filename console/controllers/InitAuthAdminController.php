<?php

namespace console\controllers;

use common\models\Admin;
use kriss\modules\auth\console\controllers\InitAuthController;

class InitAuthAdminController extends InitAuthController
{
    public $adminClass = 'common\models\Admin';
    public $superAdminId = Admin::SUPER_ADMIN_ID;
    public $authClass = 'common\models\base\AdminAuth';
    public $authOperationClass = 'common\models\AdminAuthOperation';
    public $authRoleClass = 'common\models\AdminAuthRole';
}