<?php

namespace frontend\controllers;

use common\models\base\UserAuth;
use frontend\components\ActiveDataProvider;
use common\components\MessageAlert;
use common\components\Tools;
use common\models\Department;
use common\models\User;
use frontend\components\AuthWebController;
use frontend\models\form\DepartmentCreateUpdateForm;
use kriss\modules\auth\tools\AuthValidate;
use Yii;
use yii\web\NotFoundHttpException;

class DepartmentController extends AuthWebController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => 'yii\filters\VerbFilter',
            'actions' => [
                'delete' => ['post'],
            ],
        ];

        return $behaviors;
    }

    // 列表
    public function actionIndex()
    {
        AuthValidate::run(UserAuth::DEPARTMENT_VIEW);

        $this->rememberUrl();

        $dataProvider = new ActiveDataProvider([
            'query' => Department::findInTeam(),// 找到这个公司的所有部门，放的是查询对象语句
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
        AuthValidate::run(UserAuth::DEPARTMENT_CREATE);

        $model = new DepartmentCreateUpdateForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->save()) {
                MessageAlert::set(['success' => '新建成功']);
            } else {
                MessageAlert::set(['error' => Tools::formatModelErrors2String($model->errors)]);
            }
            return $this->actionPreviousRedirect();
        }

        return $this->renderAjax('_create_update', [
            'model' => $model
        ]);
    }

    // 更新
    public function actionUpdate($id)
    {
        AuthValidate::run(UserAuth::DEPARTMENT_UPDATE);

        $model = new DepartmentCreateUpdateForm([
            'department' => $this->findModel($id)
        ]);

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

    // 删除
    public function actionDelete($id)
    {
        AuthValidate::run(UserAuth::DEPARTMENT_DELETE);

        $model = $this->findModel($id);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 将所有该部门的用户部门重置
            User::updateAll(['department_id' => 0], ['team_id' => $model->team_id, 'department_id' => $model->id]);
            // 删除部门
            $model->delete();
            $transaction->commit();
            MessageAlert::set(['success' => '删除成功']);
        } catch (\Exception $exception) {
            $transaction->rollBack();
            MessageAlert::set(['error' => $exception->getMessage()]);
        }
        return $this->actionPreviousRedirect();
    }

    /**
     * @param $id
     * @return Department
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = Department::findInTeam()->andWhere(['id' => $id])->one();
        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $model;
    }

}
