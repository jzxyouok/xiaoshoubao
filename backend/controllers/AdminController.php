<?php

namespace backend\controllers;

use backend\components\AuthWebController;
use common\components\ActiveDataProvider;
use common\components\MessageAlert;
use common\components\Tools;
use common\models\Admin;
use common\models\base\AdminAuth;
use kriss\modules\auth\actions\UserRoleUpdateAction;
use kriss\modules\auth\tools\AuthValidate;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class AdminController extends AuthWebController
{
    public function actions()
    {
        $actions = parent::actions();

        $actions['update-role'] = [
            'class' => UserRoleUpdateAction::className(),
            'permissionName' => AdminAuth::ADMIN_UPDATE_ROLE,
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
        AuthValidate::run(AdminAuth::ADMIN_VIEW);
        $this->rememberUrl();

        $dataProvider = new ActiveDataProvider([
            'query' => Admin::find(),
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                    'id' => SORT_DESC,
                ]
            ]
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    // 新增
    public function actionCreate()
    {
        AuthValidate::run(AdminAuth::ADMIN_CREATE);

        $model = new Admin();
        if ($model->load(Yii::$app->request->post())) {
            $model->generateAuthKey();
            $model->setPassword($model->password_hash);
            if ($model->validate() && $model->save(false)) {
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
        AuthValidate::run(AdminAuth::ADMIN_UPDATE);

        if ($id == Admin::SUPER_ADMIN_ID) {
            throw new ForbiddenHttpException('不能修改超级管理员信息');
        }
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

    // 重置管理员密码
    public function actionResetPassword($id)
    {
        AuthValidate::run(AdminAuth::ADMIN_RESET_PASSWORD);

        if ($id == Admin::SUPER_ADMIN_ID) {
            throw new ForbiddenHttpException('不能修改超级管理员信息');
        }
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
        AuthValidate::run(AdminAuth::ADMIN_CHANGE_STATUS);

        if ($id == Admin::SUPER_ADMIN_ID) {
            throw new ForbiddenHttpException('不能修改超级管理员信息');
        }
        $model = $this->findModel($id);
        if (
            ($model->status == Admin::STATUS_NORMAL && $status == Admin::STATUS_DISABLE)
            || ($model->status == Admin::STATUS_DISABLE && $status == Admin::STATUS_NORMAL)
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
     * @return Admin
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Admin::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException();
        }
        return $model;
    }
}
