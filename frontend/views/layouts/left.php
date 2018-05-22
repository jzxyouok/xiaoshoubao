<?php
/* @var $this \yii\web\View */

use kriss\modules\auth\tools\AuthValidate;
use common\models\base\UserAuth;

/** @var $user \common\models\User */
$user = Yii::$app->user->identity;

$menuTitle = $user->name;
$baseUrl = '';
$menu = [
    ['label' => '工作台', 'icon' => 'circle-o', 'url' => [$baseUrl . '/home/index']],
    ['label' => '企业查询', 'icon' => 'circle-o', 'url' => [$baseUrl . '/company/index'], 'visible' => AuthValidate::has(UserAuth::COMPANY_VIEW)],
    ['label' => '线索公海池', 'icon' => 'circle-o', 'url' => [$baseUrl . '/clue/index'], 'visible' => AuthValidate::has(UserAuth::CLUE_VIEW)],
    ['label' => '线索跟踪', 'icon' => 'circle-o', 'url' => [$baseUrl . '/user-clue/index'], 'visible' => AuthValidate::has(UserAuth::USER_CLUE_VIEW)],
    ['label' => '部门管理', 'icon' => 'circle-o', 'url' => [$baseUrl . '/department/index'], 'visible' => AuthValidate::has(UserAuth::DEPARTMENT_VIEW)],
    ['label' => '账户管理', 'icon' => 'circle-o', 'url' => [$baseUrl . '/user/index'], 'visible' => AuthValidate::has(UserAuth::USER_VIEW)],
    [
        'label' => '权限管理', 'icon' => 'list', 'url' => '#', 'visible' => AuthValidate::has([UserAuth::PERMISSION_VIEW, UserAuth::ROLE_VIEW]),
        'items' => [
            ['label' => '权限查看', 'icon' => 'circle-o', 'url' => [$baseUrl . '/auth/permission'], 'visible' => AuthValidate::has([UserAuth::PERMISSION_VIEW])],
            ['label' => '角色管理', 'icon' => 'circle-o', 'url' => [$baseUrl . '/auth/role'], 'visible' => AuthValidate::has([UserAuth::ROLE_VIEW])],
        ]
    ],
];
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <div class="user-panel text-center">
            <h4><?= $menuTitle ?></h4>
        </div>

        <?= dmstr\widgets\Menu::widget([
            'options' => [
                'class' => 'sidebar-menu',
                'data-widget' => 'tree'
            ],
            'items' => array_merge([
                ['label' => '菜单', 'options' => ['class' => 'header']],
            ], $menu),
        ]) ?>

    </section>

</aside>