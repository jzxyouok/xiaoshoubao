<?php

namespace backend\controllers;

use backend\components\AuthWebController;
use backend\models\CompanyUploadSearch;
use backend\models\form\CompanyUploadForm;
use common\models\base\AdminAuth;
use common\models\base\ConfigString;
use common\models\CompanyUpload;
use common\models\JobSchedule;
use console\jobs\CompanyUpload2CompanyJob;
use frontend\components\Tools;
use kriss\components\MessageAlert;
use kriss\modules\auth\tools\AuthValidate;
use Yii;
use yii\web\NotFoundHttpException;

class CompanyUploadController extends AuthWebController
{
    // 列表
    public function actionIndex()
    {
        AuthValidate::run(AdminAuth::COMPANY_UPLOAD_VIEW);
        $this->rememberUrl();

        $searchModel = new CompanyUploadSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $canSync = $this->canSyncData();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'canSync' => $canSync,
        ]);
    }

    // 导入
    public function actionImport()
    {
        AuthValidate::run(AdminAuth::COMPANY_UPLOAD_IMPORT);

        $model = new CompanyUploadForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->import()) {
            MessageAlert::set(['success' => '提交导入任务成功']);
            return $this->actionPreviousRedirect();
        }

        return $this->render('upload', [
            'model' => $model
        ]);
    }

    // 清空
    public function actionEmpty()
    {
        AuthValidate::run(AdminAuth::COMPANY_UPLOAD_EMPTY);

        CompanyUpload::deleteAll();
        MessageAlert::set(['success' => '清空成功']);
        return $this->actionPreviousRedirect();
    }

    // 更新
    public function actionUpdate($id)
    {
        AuthValidate::run(AdminAuth::COMPANY_UPLOAD_UPDATE);

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->registered_capital = $model->registered_capital . '万元人民币';
            $model->save(false);
            return $this->actionPreviousRedirect();
        }

        return $this->render('../company/create_update', [
            'model' => $model,
        ]);
    }

    // 详情
    public function actionView($id)
    {
        AuthValidate::run(AdminAuth::COMPANY_UPLOAD_VIEW);

        $model = $this->findModel($id);
        return $this->render('../company/view', [
            'model' => $model
        ]);
    }

    // 删除
    public function actionDelete($id)
    {
        AuthValidate::run(AdminAuth::COMPANY_UPLOAD_DELETE);

        $model = $this->findModel($id);
        $result = $model->delete();
        if ($result) {
            MessageAlert::set(['success' => '删除成功']);
        } else {
            MessageAlert::set(['error' => '删除失败：' . Tools::getFirstError($model->errors)]);
        }
        return $this->actionPreviousRedirect();
    }

    // 下载模板
    public function actionDownloadTemplate()
    {
        AuthValidate::run(AdminAuth::COMPANY_UPLOAD_IMPORT);

        $fileName = Yii::getAlias('@webroot/templates/company_upload_template.xls');
        if (file_exists($fileName)) {
            return Yii::$app->response->sendFile($fileName);
        } else {
            return "<h1>文件不存在</h1>";
        }
    }

    // 同步
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
     * @return CompanyUpload
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = CompanyUpload::findOne($id);
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
        if (!AuthValidate::has(AdminAuth::COMPANY_UPLOAD_SYNC)) {
            return false;
        }
        return true;
    }

    /**
     * 同步数据
     */
    protected function syncData()
    {
        Yii::$app->cache->set($this->getSyncCacheKey(), 'sync-company-upload-' . Yii::$app->user->id, 60);
        $jobSchedule = JobSchedule::createOne('同步临时企业信息到正式库');
        ConfigString::getQueue()->push(new CompanyUpload2CompanyJob([
            'jobScheduleId' => $jobSchedule->id,
            'operateAdminId' => Yii::$app->user->id
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
