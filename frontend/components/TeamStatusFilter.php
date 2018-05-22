<?php

namespace frontend\components;

use Yii;
use yii\base\ActionFilter;
use yii\base\Exception;

class TeamStatusFilter extends ActionFilter
{
    /**
     * 不允许访问的状态
     * @var array
     */
    public $notAllowedStatus = [];
    /**
     * 拒绝时跳转的界面
     * @var array
     */
    public $redirectUrl = ['/site/login'];

    public function beforeAction($action)
    {
        $cache = Yii::$app->cache;
        if (!Yii::$app->user->isGuest) {
            return $cache->getOrSet([Yii::$app->user->id, __CLASS__, __FUNCTION__], function () use ($action) {
                return $this->doAction($action);
            }, 60);
        }
        return $this->doAction($action);
    }

    /**
     * @param $action
     * @return bool
     */
    protected function doAction($action)
    {
        try {
            $user = Tools::getCurrentUser();
            $team = $user->team;
            if (!$team) {
                throw new Exception('团队不存在');
            }
            if ($team->past_at < time()) {
                throw new Exception('团队已过期，请联系管理员');
            }
            if (in_array($team->status, $this->notAllowedStatus)) {
                throw new Exception('团队状态不可操作');
            }
            return parent::beforeAction($action);
        } catch (Exception $exception) {
            Yii::$app->session->setFlash('error', $exception->getMessage());
            Yii::$app->getResponse()->redirect($this->redirectUrl);
            return false;
        }
    }
}