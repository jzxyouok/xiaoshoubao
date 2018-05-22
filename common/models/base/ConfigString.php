<?php

namespace common\models\base;

use common\components\QiNiu;
use common\components\SinglePointLoginToken;
use trntv\filekit\Storage;
use Yii;
use yii\elasticsearch\Connection;
use yii\queue\redis\Queue;

class ConfigString
{
    // component
    const COMPONENT_REDIS = 'redis';
    const COMPONENT_CACHE_REDIS = 'cache_redis';
    const COMPONENT_SESSION_REDIS = 'session_redis';
    const COMPONENT_QI_NIU = 'qi_niu';
    const COMPONENT_FILE_STORAGE = 'fileStorage';
    const COMPONENT_ELASTIC_SEARCH = 'elasticsearch';
    const COMPONENT_QUEUE = 'queue';
    const COMPONENT_SINGLE_POINT_LOGIN_TOKEN = 'single_point_login_token';

    // log category
    const CATEGORY_NEED_SOLVED = 'need-solved';
    const CATEGORY_COMPANY_MIGRATION = 'company-migration';

    /**
     * @return null|object|QiNiu
     */
    public static function getQiNiu()
    {
        return Yii::$app->get(static::COMPONENT_QI_NIU);
    }

    /**
     * @return \League\Flysystem\Filesystem
     */
    public static function getDisk()
    {
        return static::getQiNiu()->getDisk();
    }

    /**
     * @return null|object|Connection
     */
    public static function getElasticSearch()
    {
        return Yii::$app->get(static::COMPONENT_ELASTIC_SEARCH);
    }

    /**
     * @return null|object|Queue
     */
    public static function getQueue()
    {
        return Yii::$app->get(static::COMPONENT_QUEUE);
    }

    /**
     * @return null|object|SinglePointLoginToken
     */
    public static function getSinglePointLoginToken()
    {
        return Yii::$app->get(static::COMPONENT_SINGLE_POINT_LOGIN_TOKEN);
    }

    /**
     * @return null|object|Storage
     */
    public static function getFileStorage(){
        return Yii::$app->get(static::COMPONENT_FILE_STORAGE);
    }
}