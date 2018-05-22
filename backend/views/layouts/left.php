<?php
/* @var $this \yii\web\View */

use common\models\base\AdminAuth;
use kriss\modules\auth\tools\AuthValidate;
use common\models\Admin;

/** @var $admin Admin */
$admin = Yii::$app->user->identity;

$menuTitle = '总管理后台';
$baseUrl = '';
$menu = [
    ['label' => '工作台', 'icon' => 'circle-o', 'url' => [$baseUrl . '/home/index']],
    ['label' => '企业管理', 'icon' => 'circle-o', 'url' => [$baseUrl . '/company/index'], 'visible' => AuthValidate::has(AdminAuth::COMPANY_VIEW)],
    ['label' => '企业上传管理', 'icon' => 'circle-o', 'url' => [$baseUrl . '/company-upload/index'], 'visible' => AuthValidate::has(AdminAuth::COMPANY_UPLOAD_VIEW)],
    ['label' => '任务管理', 'icon' => 'circle-o', 'url' => [$baseUrl . '/job-schedule/index'], 'visible' => AuthValidate::has(AdminAuth::JOB_SCHEDULE_VIEW)],
    [
        'label' => '地市管理', 'icon' => 'list', 'url' => '#',
        'items' => [
            ['label' => '省级管理', 'icon' => 'circle-o', 'url' => [$baseUrl . '/province/index']],
            ['label' => '城市管理', 'icon' => 'circle-o', 'url' => [$baseUrl . '/city/index']],
            ['label' => '地区管理', 'icon' => 'circle-o', 'url' => [$baseUrl . '/area/index']],
        ]
    ],
    ['label' => '团队管理', 'icon' => 'circle-o', 'url' => [$baseUrl . '/team/index'], 'visible' => AuthValidate::has(AdminAuth::TEAM_VIEW)],
    ['label' => '管理员管理', 'icon' => 'circle-o', 'url' => [$baseUrl . '/admin'], 'visible' => AuthValidate::has(AdminAuth::ADMIN_VIEW)],
    [
        'label' => '权限管理', 'icon' => 'list', 'url' => '#', 'visible' => AuthValidate::has([AdminAuth::PERMISSION_VIEW, AdminAuth::ROLE_VIEW]),
        'items' => [
            ['label' => '权限查看', 'icon' => 'circle-o', 'url' => [$baseUrl . '/auth/permission'], 'visible' => AuthValidate::has([AdminAuth::PERMISSION_VIEW])],
            ['label' => '角色管理', 'icon' => 'circle-o', 'url' => [$baseUrl . '/auth/role'], 'visible' => AuthValidate::has([AdminAuth::ROLE_VIEW])],
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