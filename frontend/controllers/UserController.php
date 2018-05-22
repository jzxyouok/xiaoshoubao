<?php

namespace frontend\controllers;

use common\models\base\UserAuth;
use frontend\components\ActiveDataProvider;
use common\components\MessageAlert;
use common\components\Tools;
use common\models\User;
use frontend\components\AuthWebController;
use kriss\modules\auth\actions\UserRoleUpdateAction;
use kriss\modules\auth\tools\AuthValidate;
use Yii;
use yii\web\NotFoundHttpException;

class UserController extends AuthWebController
{
    public function actions()
    {
        $actions = parent::actions();

        $actions['update-role'] = [
            'class' => UserRoleUpdateAction::className(),
            'permissionName' => UserAuth::USER_UPDATE_ROLE,
            'isRenderAjax' => true,
            'successCallback' => function ($action, $result) {
                /** @var $action UserRoleUpdateAction */
                if ($result['type'] == 'success') {
                    Yii::$app->session->setFlash('success', '授权成功');
                } else {
                    Yii::$app->session->setFlash('error', '授权失败：' . $result['msg']);
                }
                /** @var self $controller */
                $controller = $action->controller;
                return $controller->actionPreviousRedirect();
            }
        ];

        return $actions;
    }

    // 列表
    public function actionIndex()
    {
        AuthValidate::run(UserAuth::USER_VIEW);
        $this->rememberUrl();

        $dataProvider = new ActiveDataProvider([
            'query' => User::findInTeam()->with('department'),
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    // 新增
    public function actionCreate()
    {
        AuthValidate::run(UserAuth::USER_CREATE);

        $model = new User();
        $model->linkTeam();

        if ($model->load(Yii::$app->request->post())) {
            $model->setPassword($model->password_hash);
            $model->setNormalType();
            if ($model->validate() && $model->save()) {
                MessageAlert::set(['success' => '新建成功']);
            } else {
                MessageAlert::set(['error' => Tools::formatModelErrors2String($model->errors)]);
            }
            return $this->actionPreviousRedirect();
        }

        return $this->renderAjax('_create_update', [
            'model' => $model,
        ]);
    }

    // 更新
    public function actionUpdate($id)
    {
        AuthValidate::run(UserAuth::USER_UPDATE);

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->save()) {
                MessageAlert::set(['success' => '修改成功']);
            } else {
                MessageAlert::set(['error' => Tools::formatModelErrors2String($model->errors)]);
            }
            return $this->actionPreviousRedirect();
        }

        return $this->renderAjax('_create_update', [
            'model' => $model
        ]);
    }

    // 重置密码
    public function actionResetPassword($id)
    {
        AuthValidate::run(UserAuth::USER_RESET_PASSWORD);

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->setPassword($model->password_hash);

            if ($model->validate() && $model->save()) {
                MessageAlert::set(['success' => '重置密码成功']);
            } else {
                MessageAlert::set(['error' => Tools::formatModelErrors2String($model->errors)]);
            }
            return $this->actionPreviousRedirect();
        }

        return $this->renderAjax('_reset_password', [
            'model' => $model
        ]);
    }

    // 修改状态
    public function actionChangeStatus($id, $status)
    {
        AuthValidate::run(UserAuth::USER_CHANGE_STATUS);

        $model = $this->findModel($id);
        if ($model->type == User::TYPE_ADMIN || $model->id == Yii::$app->user->id) {
            MessageAlert::set(['error' => '不能操作该账户']);
            return $this->actionPreviousRedirect();
        }
        if (
            ($model->status == User::STATUS_NORMAL && $status == User::STATUS_DISABLE)
            || ($model->status == User::STATUS_DISABLE && $status == User::STATUS_NORMAL)
        ) {
            $model->status = $status;
            $model->save(false);
            MessageAlert::set(['success' => '操作成功']);
        } else {
            MessageAlert::set(['error' => '当前状态下操作失败']);
        }
        return $this->actionPreviousRedirect();
    }

    /**
     * @param $id
     * @return User
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = User::findInTeam()->andWhere(['id' => $id])->one();
        if (!$model) {
            throw new NotFoundHttpException();
        }
        return $model;
    }

}
