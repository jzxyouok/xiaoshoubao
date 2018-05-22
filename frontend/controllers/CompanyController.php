<?php

namespace frontend\controllers;

use common\models\base\UserAuth;
use common\models\elasticsearch\Company;
use frontend\components\AuthWebController;
use frontend\models\CompanySearch;
use kriss\modules\auth\tools\AuthValidate;
use Yii;

class CompanyController extends AuthWebController
{
    // 列表
    public function actionIndex()
    {
        AuthValidate::run(UserAuth::COMPANY_VIEW);

        $this->rememberUrl();

        $searchModel = new CompanySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    // 详情
    public function actionView($id)
    {
        AuthValidate::run(UserAuth::COMPANY_VIEW);

        $model = Company::findOne($id);
        return $this->render('view', [
            'model' => $model
        ]);
    }

}
