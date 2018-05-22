<?php

use common\models\base\ConfigString;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    // 网站维护，打开以下注释
    //'catchAll' => ['site/offline'],
    'modules' => [
        'auth' => [
            'class' => \kriss\modules\auth\Module::className(),
            'as user_login' => \kriss\behaviors\web\UserLoginFilter::className(),
            'as iframe_layout' => \backend\components\IframeLayoutAction::className(),
            'authRoleClass' => \common\models\UserAuthRole::className(),
            'authOperationClass' => \common\models\UserAuthOperation::className(),
            'skipAuthOptions' => []
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'class' => \kriss\modules\auth\components\User::className(),
            'authClass' => \common\models\base\UserAuth::className(),
            'authRoleClass' => \common\models\UserAuthRole::className(),
            'identityClass' => \common\models\User::className(),
            'superAdminId' => 0,
            'enableAutoLogin' => false,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            'name' => 'front-session',
            'class' => 'yii\redis\Session',
            'redis' => ConfigString::COMPONENT_SESSION_REDIS,
            'keyPrefix' => 'app_fs_',
            'timeout' => 3 * 3600
        ],
        ConfigString::COMPONENT_SINGLE_POINT_LOGIN_TOKEN => [
            'class' => \common\components\SinglePointLoginToken::className(),
        ],
        'log' => [
            //'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [],
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
