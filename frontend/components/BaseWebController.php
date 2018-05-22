<?php

namespace frontend\components;

use common\components\Tools;
use kriss\traits\WebControllerTrait;
use yii\web\Controller;
use yii\web\Response;

class BaseWebController extends Controller
{
    use WebControllerTrait;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        return $behaviors;
    }

    /**
     * 返回 json 正确的状态
     * @param $data
     * @return Response
     */
    public function asJsonOk($data)
    {
        $data = [
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ];
        return $this->asJson($data);
    }

    /**
     * 返回 json 错误的状态
     * @param $msg
     * @return Response
     */
    public function asJsonError($msg)
    {
        $data = [
            'code' => 422,
            'msg' => $msg
        ];
        return $this->asJson($data);
    }

    /**
     * 返回 json 错误状态，通过 model
     * @param $model
     * @return Response
     */
    public function asJsonErrorWithModel($model){
        return $this->asJsonError(Tools::getFirstError($model->errors));
    }
}