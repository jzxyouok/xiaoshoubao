<?php
use common\models\base\ConfigString;

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=172.17.0.1;dbname=xiaoshoubao',
            'username' => 'xiaoshoubao',
            'password' => 'XSB@12345687',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 600,
            'schemaCacheExclude' => [],
            'schemaCache' => 'cache',
            'queryCache' => 'cache',
        ],
        ConfigString::COMPONENT_ELASTIC_SEARCH => [
            'class' => 'yii\elasticsearch\Connection',
            'nodes' => [
                ['http_address' => '172.17.0.1:9200'],
            ],
        ],
        ConfigString::COMPONENT_REDIS => [
            'class' => 'yii\redis\Connection',
            'hostname' => '172.17.0.1',
            'port' => 6379,
            'database' => 0,
        ],
        ConfigString::COMPONENT_SESSION_REDIS => [
            'class' => 'yii\redis\Connection',
            'hostname' => '172.17.0.1',
            'port' => 6379,
            'database' => 1,
        ],
        ConfigString::COMPONENT_CACHE_REDIS => [
            'class' => 'yii\redis\Connection',
            'hostname' => '172.17.0.1',
            'port' => 6379,
            'database' => 2,
        ],
        ConfigString::COMPONENT_QI_NIU => [
            'class' => \common\components\QiNiu::className(),
            'access_key' => '0c9ODbEP2XGHaTyxDdXqM1Bh6egcg8dLaXOOZnUR',
            'secret_key' => 'jBkDQQe_jUVaavigcFwGZB_x6xyOKouNasYlZ_HN',
            'bucket' => 'baifu-test',
            'domain' => 'oi9q322b0.bkt.clouddn.com',
            'savePath' => 'fxbjy/'
        ],
    ],
];
