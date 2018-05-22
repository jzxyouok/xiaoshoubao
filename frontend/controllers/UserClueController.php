<?php

namespace frontend\controllers;

use common\components\MessageAlert;
use common\models\base\UserAuth;
use common\models\UserClue;
use frontend\components\AuthWebController;
use frontend\components\Tools;
use frontend\models\form\UserCluePickForm;
use frontend\models\form\UserClueRecordForm;
use frontend\models\form\UserClueReturnForm;
use frontend\models\UserClueSearch;
use kriss\modules\auth\tools\AuthValidate;
use Yii;
use yii\web\NotFoundHttpException;

class UserClueController extends AuthWebController
{
    // 列表
    public function actionIndex()
    {
        AuthValidate::run(UserAuth::USER_CLUE_VIEW);
        $this->rememberUrl();

        $searchModel = new UserClueSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    // 领取线索
    public function actionPick()
    {
        AuthValidate::run(UserAuth::USER_CLUE_PICK);

        $model = new UserCluePickForm([
            'type' => UserCluePickForm::TYPE_PICK,
            'user_id' => Tools::getCurrentUserId()
        ]);
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

    // 跟进记录
    public function actionRecord($id, $redirect = true)
    {
        AuthValidate::run(UserAuth::USER_CLUE_RECORD);

        $userClue = $this->findModel($id);
        $model = new UserClueRecordForm([
            'userClue' => $userClue,
        ]);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->create()) {
                MessageAlert::set(['success' => '记录成功']);
            } else {
                MessageAlert::set(['error' => Tools::getFirstError($model->errors)]);
            }
            if ($redirect) {
                return $this->actionPreviousRedirect();
            } else {
                return $this->redirect(Yii::$app->request->referrer);
            }
        }
        return $this->renderAjax('_record', [
            'model' => $model
        ]);
    }

    // 退回
    public function actionReturn($id, $redirect = true)
    {
        AuthValidate::run(UserAuth::USER_CLUE_RETURN);

        $userClue = $this->findModel($id);
        $model = new UserClueReturnForm([
            'user_id' => Tools::getCurrentUserId(),
            'company_id' => $userClue->company_id,
        ]);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $result = $model->return();
                MessageAlert::set([$result['type'] => $result['msg']]);
            } else {
                MessageAlert::set(['error' => Tools::getFirstError($model->errors)]);
            }
            if ($redirect) {
                return $this->actionPreviousRedirect();
            } else {
                return $this->redirect(Yii::$app->request->referrer);
            }
        }
        return $this->renderAjax('_return', [
            'model' => $model
        ]);
    }

    /**
     * @param $id
     * @return array|UserClue|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = UserClue::findInTeam()
            ->andWhere(['id' => $id, 'user_id' => Tools::getCurrentUserId()])
            ->one();
        if (!$model) {
            throw new NotFoundHttpException('未找到该线索');
        }
        return $model;
    }
}
