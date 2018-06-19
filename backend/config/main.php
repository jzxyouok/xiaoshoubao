<?php

use common\models\base\ConfigString;
// 再来个测试
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    // 网站维护，打开以下注释
    //'catchAll' => ['site/offline'],
    'modules' => [
        'auth' => [
            'class' => \kriss\modules\auth\Module::className(),
            'as user_login' => \kriss\behaviors\web\UserLoginFilter::className(),
            'as iframe_layout' => \backend\components\IframeLayoutAction::className(),
            'authRoleClass' => \common\models\AdminAuthRole::className(),
            'authOperationClass' => \common\models\AdminAuthOperation::className(),
            'skipAuthOptions' => []
        ],
        'log-reader' => [
            'class' => 'kriss\logReader\Module',
            'as login-filter' => \kriss\behaviors\web\UserLoginFilter::class,
            'aliases' => [
                'frontend' => '@frontend/runtime/logs/app.log',
                'backend' => '@backend/runtime/logs/app.log',
                'console' => '@console/runtime/logs/app.log',
                'needSolved' => '@common/runtime/logs/needSolved/needSolved.log',
                'companyMigration' => '@common/runtime/logs/companyMigration/companyMigration.log',
            ],
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        ConfigString::COMPONENT_FILE_STORAGE => [
            'class' => \trntv\filekit\Storage::className(),
            'baseUrl' => '@web/uploads',
            'filesystem' => function () {
                $adapter = new \League\Flysystem\Adapter\Local(Yii::getAlias('@webroot/uploads'));
                return new League\Flysystem\Filesystem($adapter);
            }
        ],
        'user' => [
            'class' => \kriss\modules\auth\components\User::className(),
            'authClass' => \common\models\base\AdminAuth::className(),
            'authRoleClass' => \common\models\AdminAuthRole::className(),
            'identityClass' => \common\models\Admin::className(),
            'enableAutoLogin' => false,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            'name' => 'back-session',
            'class' => 'yii\redis\Session',
            'redis' => ConfigString::COMPONENT_SESSION_REDIS,
            'keyPrefix' => 'app_bs_',
            'timeout' => 6 * 3600
        ],
        ConfigString::COMPONENT_SINGLE_POINT_LOGIN_TOKEN => [
            'class' => \common\components\SinglePointLoginToken::className(),
        ],
        'log' => [
            //'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-blue',
                ],
            ],
        ],
    ],
    'params' => $params,
];
