<?php

namespace backend\controllers;

use backend\components\AuthWebController;
use backend\models\JobScheduleSearch;
use common\models\base\AdminAuth;
use kriss\modules\auth\tools\AuthValidate;
use Yii;

class JobScheduleController extends AuthWebController
{
    // 列表
    public function actionIndex()
    {
        AuthValidate::run(AdminAuth::JOB_SCHEDULE_VIEW);
        $this->rememberUrl();

        $searchModel = new JobScheduleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

}
