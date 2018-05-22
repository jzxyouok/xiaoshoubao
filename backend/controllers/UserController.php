<?php

namespace backend\controllers;

use backend\components\AuthWebController;
use backend\models\UserSearch;
use common\models\base\AdminAuth;
use common\models\Team;
use kriss\modules\auth\tools\AuthValidate;
use Yii;

class UserController extends AuthWebController
{
    // 成员列表
    public function actionIndex($team_id)
    {
        AuthValidate::run(AdminAuth::TEAM_VIEW);
        $this->rememberUrl();

        $team = Team::findOne($team_id);

        $searchModel = new UserSearch([
            'team_id' => $team_id
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'team' => $team
        ]);
    }
}
