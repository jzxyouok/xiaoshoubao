<?php

namespace backend\controllers;

use backend\components\AuthWebController;
use backend\models\CompanySearch;
use common\components\MessageAlert;
use common\components\Tools;
use common\models\base\AdminAuth;
use common\models\base\ConfigString;
use common\models\Company;
use common\models\CompanyMigration;
use common\models\JobSchedule;
use console\jobs\Company2EsJob;
use kriss\modules\auth\tools\AuthValidate;
use Yii;
use yii\web\NotFoundHttpException;

class CompanyController extends AuthWebController
{
    // 列表
    public function actionIndex()
    {
        AuthValidate::run(AdminAuth::COMPANY_VIEW);
        $this->rememberUrl();

        $searchModel = new CompanySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $canSync = $this->canSyncData();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'canSync' => $canSync
        ]);
    }

    // 新增
    public function actionCreate()
    {
        AuthValidate::run(AdminAuth::COMPANY_CREATE);

        $model = new Company();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->registered_capital = $model->registered_capital . '万元人民币';
            $model->save(false);
            return $this->actionPreviousRedirect();
        }

        return $this->render('create_update', [
            'model' => $model,
        ]);
    }

    // 更新
    public function actionUpdate($id)
    {
        AuthValidate::run(AdminAuth::COMPANY_UPDATE);

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->registered_capital = $model->registered_capital . '万元人民币';
            $model->save(false);
            return $this->actionPreviousRedirect();
        }

        return $this->render('create_update', [
            'model' => $model,
        ]);
    }

    // 详情
    public function actionView($id)
    {
        AuthValidate::run(AdminAuth::COMPANY_VIEW);

        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model
        ]);
    }

    // 删除
    public function actionDelete($id)
    {
        AuthValidate::run(AdminAuth::COMPANY_DELETE);

        $model = $this->findModel($id);
        $result = $model->delete();
        if ($result) {
            MessageAlert::set(['success' => '删除成功']);
        } else {
            MessageAlert::set(['error' => '删除失败：' . Tools::getFirstError($model->errors)]);
        }
        return $this->actionPreviousRedirect();
    }

    // 修改状态
    public function actionChangeStatus($id, $status)
    {
        AuthValidate::run(AdminAuth::COMPANY_CHANGE_STATUS);

        $model = $this->findModel($id);
        if (
            ($model->status == Company::STATUS_NORMAL && $status == Company::STATUS_DISABLE)
            || ($model->status == Company::STATUS_DISABLE && $status == Company::STATUS_NORMAL)
        ) {
            $model->status = $status;
            $model->save(false);
            MessageAlert::set(['success' => '操作成功']);
        } else {
            MessageAlert::set(['error' => '当前状态下操作失败']);
        }
        return $this->actionPreviousRedirect();
    }

    // 同步数据
    public function actionSync()
    {
        if (!$this->canSyncData()) {
            MessageAlert::set(['warning' => '操作失败：已经正在执行同步操作，请勿在短时间内重复操作']);
        } else {
            $this->syncData();
            MessageAlert::set(['success' => '提交同步任务成功']);
        }
        return $this->actionPreviousRedirect();
    }

    /**
     * @param $id
     * @return Company
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Company::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('未找到该企业');
        }
        return $model;
    }

    /**
     * 是否可以同步数据
     * @return bool
     */
    protected function canSyncData()
    {
        $value = Yii::$app->cache->get($this->getSyncCacheKey());
        if ($value) {
            return false;
        }
        if (!AuthValidate::has(AdminAuth::COMPANY_SYNC)) {
            return false;
        }
        return (bool)CompanyMigration::find()->where(['status' => CompanyMigration::STATUS_WAIT])->limit(1)->one();
    }

    /**
     * 同步数据
     */
    protected function syncData()
    {
        Yii::$app->cache->set($this->getSyncCacheKey(), 'sync-company-' . Yii::$app->user->id, 60);
        $jobSchedule = JobSchedule::createOne('同步企业信息到查询库');
        ConfigString::getQueue()->push(new Company2EsJob([
            'jobScheduleId' => $jobSchedule->id
        ]));
    }

    /**
     * 获取同步数据的缓存 key
     * @return array
     */
    protected function getSyncCacheKey()
    {
        return [__CLASS__, __FUNCTION__, Yii::$app->user->id];
    }

}
