<?php

namespace backend\controllers;

use backend\components\AuthWebController;
use backend\models\form\TeamCreateForm;
use backend\models\TeamSearch;
use common\components\MessageAlert;
use common\components\Tools;
use common\models\base\AdminAuth;
use common\models\LogOperation;
use common\models\Team;
use common\models\User;
use kriss\modules\auth\tools\AuthValidate;
use Yii;
use yii\web\NotFoundHttpException;

class TeamController extends AuthWebController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => 'yii\filters\VerbFilter',
            'actions' => [
                'change-status' => ['post'],
            ],
        ];

        return $behaviors;
    }

    // 列表
    public function actionIndex()
    {
        AuthValidate::run(AdminAuth::TEAM_VIEW);
        $this->rememberUrl();

        $searchModel = new TeamSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    // 新增
    public function actionCreate()
    {
        AuthValidate::run(AdminAuth::TEAM_CREATE);

        $team = new Team();
        $user = new User();
        if ($team->load(Yii::$app->request->post()) && $user->load(Yii::$app->request->post())) {
            if ($team->validate() && $user->validate()) {
                $form = new TeamCreateForm([
                    'team' => $team,
                    'user' => $user
                ]);
                $result = $form->create();
                MessageAlert::set([$result['type'] => $result['msg']]);
                if ($result['type'] == 'success') {
                    return $this->actionPreviousRedirect();
                }
            }
        }
        return $this->render('create', [
            'team' => $team,
            'user' => $user,
        ]);
    }

    // 更新
    public function actionUpdate($id)
    {
        AuthValidate::run(AdminAuth::TEAM_UPDATE);

        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->save(false)) {
                LogOperation::recordAdminOperation('修改团队信息:' . $model->name, $model->toArray());
                MessageAlert::set(['success' => '修改成功']);
            } else {
                MessageAlert::set(['error' => Tools::formatModelErrors2String($model->errors)]);
            }
            return $this->actionPreviousRedirect();
        }
        return $this->renderAjax('_update', [
            'model' => $model
        ]);
    }

    // 成员信息
    public function actionView($id)
    {
        AuthValidate::run(AdminAuth::TEAM_VIEW);

        return $this->redirect(['user/index', 'team_id' => $id]);
    }

    // 重置管理员密码
    public function actionResetPassword($id)
    {
        AuthValidate::run(AdminAuth::TEAM_RESET_PASSWORD);

        $model = User::find()->where(['team_id' => $id, 'type' => User::TYPE_ADMIN])->one();

        if ($model->load(Yii::$app->request->post())) {
            $model->setPassword($model->password_hash);
            if ($model->validate() && $model->save()) {
                LogOperation::recordAdminOperation('重置团队管理员密码:' . $model->name, $model->toArray());
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
        AuthValidate::run(AdminAuth::TEAM_CHANGE_STATUS);

        $model = $this->findModel($id);
        if (
            ($model->status == Team::STATUS_NORMAL && $status == Team::STATUS_DISABLE)
            || ($model->status == Team::STATUS_DISABLE && $status == Team::STATUS_NORMAL)
        ) {
            $model->status = $status;
            $model->save(false);
            LogOperation::recordAdminOperation('修改团队状态', $model->toArray());
            MessageAlert::set(['success' => '操作成功']);
        } else {
            MessageAlert::set(['error' => '当前状态下操作失败']);
        }
        return $this->actionPreviousRedirect();
    }

    /**
     * @param $id
     * @return Team
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Team::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException();
        }
        return $model;
    }

}
