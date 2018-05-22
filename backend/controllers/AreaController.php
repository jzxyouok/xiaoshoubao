<?php

namespace backend\controllers;

use backend\components\AuthWebController;
use backend\models\AreaSearch;
use common\models\Area;
use frontend\components\Tools;
use Yii;
use yii\web\NotFoundHttpException;
use common\components\MessageAlert;

class AreaController extends AuthWebController
{
    // 列表
    public function actionIndex()
    {
        $this->rememberUrl();

        $searchModel = new AreaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    // 新增
    public function actionCreate()
    {
        $model = new Area();
        if($model->load(Yii::$app->request->post()) ){
            if ($model->validate() && $model->save(false)) {
                MessageAlert::set(['success' => '添加成功']);
            } else {
                MessageAlert::set(['error' => '添加失败：' . Tools::formatModelErrors2String($model->errors)]);
            }
            return $this->actionPreviousRedirect();
        }
        return $this->renderAjax('_create_update',[
            'model' => $model
        ]);
    }

    // 更新
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post())){
            if($model->validate() && $model->save(false)){
                MessageAlert::set(['success' => '修改成功']);
            } else {
                MessageAlert::set(['error' => '修改失败：' . Tools::formatModelErrors2String($model->errors)]);
            }
            return $this->actionPreviousRedirect();
        }
        return $this->renderAjax('_create_update',[
            'model' => $model
        ]);
    }

    // 删除
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $result = $model->delete();
        if($result){
            MessageAlert::set(['success' => '删除成功']);
        }else{
            MessageAlert::set(['error' => '添加失败：' . Tools::getFirstError($model->errors)]);
        }
        return $this->actionPreviousRedirect();
    }

    /**
     * @param $id
     * @return null|static|Area
     * @throws NotFoundHttpException
     */
    protected function findModel($id){
        $model =  Area::findOne($id);
        if(!$model){
            throw new NotFoundHttpException();
        }
        return $model;
    }
}
