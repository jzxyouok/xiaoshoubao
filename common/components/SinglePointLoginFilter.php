<?php

namespace common\components;

use common\models\base\ConfigString;
use Yii;
use yii\base\ActionFilter;

class SinglePointLoginFilter extends ActionFilter
{
    /**
     * 未登录时的跳转地址
     * @var array
     */
    public $loginUrl = ['/site/login'];

    public function beforeAction($action)
    {
        if (!ConfigString::getSinglePointLoginToken()->check()) {
            MessageAlert::set(['error' => '登录已失效，请重新登录']);
            Yii::$app->getResponse()->redirect($this->loginUrl);
            return false;
        }
        return parent::beforeAction($action);
    }
}