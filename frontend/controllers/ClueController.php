<?php

namespace frontend\controllers;

use common\models\base\UserAuth;
use common\models\Clue;
use frontend\components\AuthWebController;
use frontend\components\Tools;
use frontend\models\ClueSearch;
use frontend\models\form\CluePickForm;
use frontend\models\form\UserCluePickForm;
use kriss\components\MessageAlert;
use kriss\modules\auth\tools\AuthValidate;
use Yii;
use yii\web\NotFoundHttpException;

class ClueController extends AuthWebController
{
    // 列表
    public function actionIndex()
    {
        AuthValidate::run(UserAuth::CLUE_VIEW);
        $this->rememberUrl();

        $searchModel = new ClueSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    // 领取线索
    public function actionPick()
    {
        AuthValidate::run(UserAuth::CLUE_PICK);

        $model = new CluePickForm();
        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            $result = $model->create();
            if ($result['type'] == 'success') {
                return $this->asJsonOk($result['msg']);
            } else {
                return $this->asJsonError($result['msg']);
            }
        }
        return $this->asJsonErrorWithModel($model);
    }

    // 分配线索
    public function actionDistribute($id, $redirect = true)
    {
        AuthValidate::run(UserAuth::CLUE_DISTRIBUTE);

        $clue = $this->findModel($id);
        $model = new UserCluePickForm([
            'type' => UserCluePickForm::TYPE_DISTRIBUTE,
            'company_id' => $clue->company_id,
        ]);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $result = $model->create();
                MessageAlert::set([$result['type'] => $result['msg']]);
            } else {
                MessageAlert::set(['error' => Tools::getFirstError($model->errors)]);
            }
            if($redirect) {
                return $this->actionPreviousRedirect();
            } else {
                return $this->redirect(Yii::$app->request->referrer);
            }
        }
        return $this->renderAjax('_distribute', [
            'model' => $model
        ]);
    }

    // 批量分配线索
    public function actionDistributeMulti()
    {
        AuthValidate::run(UserAuth::CLUE_DISTRIBUTE);

        $model = new UserCluePickForm([
            'type' => UserCluePickForm::TYPE_DISTRIBUTE,
        ]);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $result = $model->create();
                MessageAlert::set([$result['type'] => $result['msg']]);
            } else {
                MessageAlert::set(['error' => Tools::getFirstError($model->errors)]);
            }
            return $this->actionPreviousRedirect();
        }
        $companyIds = Yii::$app->request->post('keys');
        $model->company_ids = $companyIds ? implode(',', $companyIds) : '';
        return $this->renderAjax('_distribute', [
            'model' => $model
        ]);
    }

    /**
     * @param $id
     * @return array|Clue|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = Clue::findInTeam()->andWhere(['id' => $id])->one();
        if (!$model) {
            throw new NotFoundHttpException('未找到该线索');
        }
        return $model;
    }
}
