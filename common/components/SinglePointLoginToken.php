<?php

namespace common\components;

use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\caching\Cache;
use yii\caching\FileCache;
use yii\web\ForbiddenHttpException;

class SinglePointLoginToken extends BaseObject
{
    /**
     * @var string|array|\yii\caching\Cache
     */
    public $cache = 'cache';
    /**
     * key 前缀
     * @var string
     */
    public $keyPrefix = 'sp_token_';
    /**
     * 缓存时间
     * 默认7天
     * @var int
     */
    public $cacheTime = 604800;

    public function init()
    {
        if (is_string($this->cache)) {
            $this->cache = Yii::$app->get($this->cache);
        } elseif (is_array($this->cache)) {
            if (!isset($this->cache['class'])) {
                $this->cache['class'] = FileCache::className();
            }
            $this->cache = Yii::createObject($this->cache);
        }
        if (!$this->cache instanceof Cache) {
            throw new InvalidConfigException("cache 必须是 yii\caching\Cache 的实例");
        }
        parent::init();
    }

    /**
     * 设置并获取
     */
    public function update()
    {
        $key = $this->getCacheKey();
        $value = $this->generateValue();
        // 在 session 中设置
        Yii::$app->session->set($key, md5($value));
        // 保存到 cache
        Yii::$app->cache->set($key, $value);
    }

    /**
     * 检查 token
     * @return bool
     */
    public function check()
    {
        $key = $this->getCacheKey();
        $cacheValue = Yii::$app->cache->get($key);
        $sessionValue = Yii::$app->session->get($key);
        if ($cacheValue && $sessionValue && md5($cacheValue) == $sessionValue) {
            return true;
        }
        return false;
    }

    /**
     * 获取 key
     * @return string
     * @throws ForbiddenHttpException
     */
    protected function getCacheKey()
    {
        if (!$userId = Yii::$app->user->id) {
            throw new ForbiddenHttpException('用户必须登录');
        }
        return $this->keyPrefix . md5($userId . Yii::$app->id);
    }

    /**
     * 获取随机值
     * @return string
     */
    protected function generateValue()
    {
        return date('YmdHis') . '-' . rand(1000000, 9999999);
    }
}