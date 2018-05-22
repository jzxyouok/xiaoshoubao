<?php

namespace console\controllers;

use common\models\Admin;
use common\models\Team;
use common\models\User;
use yii\console\Controller;

class InitController extends Controller
{
    public function actionInitData()
    {
        $this->initAdmin();
        //$this->initTeam();
        //$this->initUser();
    }

    protected function initAdmin()
    {
        $model = new Admin();
        $model->id = Admin::SUPER_ADMIN_ID;
        $model->username = 'admin';
        $model->setPassword(123456);
        $model->generateAuthKey();
        $model->name = '超级管理员';
        $model->save();
    }

    protected function initTeam()
    {
        $model = new Team();
        $model->id = 1;
        $model->name = 'XX公司';
        $model->save(false);
    }

    protected function initUser()
    {
        $model = new User();
        $model->team_id = 1;
        $model->type = User::TYPE_ADMIN;
        $model->cellphone = '12345678910';
        $model->setPassword(123456);
        $model->generateAuthKey();
        $model->name = '销售经理';
        $model->save();

        $model = new User();
        $model->team_id = 1;
        $model->type = User::TYPE_USER;
        $model->cellphone = '12345678911';
        $model->setPassword(123456);
        $model->generateAuthKey();
        $model->name = '销售1号';
        $model->save();
    }
}