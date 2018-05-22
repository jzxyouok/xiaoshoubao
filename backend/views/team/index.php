<?php
/** @var $this yii\web\View */
/** @var $dataProvider common\components\ActiveDataProvider */
/** @var $searchModel backend\models\TeamSearch */

use common\models\base\AdminAuth;
use common\models\Team;
use common\widgets\SimpleDynaGrid;
use kriss\modules\auth\tools\AuthValidate;
use yii\helpers\Html;

$this->title = '团队列表';
$this->params['breadcrumbs'] = [
    '团队管理',
    $this->title,
];

echo $this->render('_search', [
    'model' => $searchModel,
]);

$columns = [
    [
        'attribute' => 'name',
    ],
    [
        'attribute' => 'status',
        'value' => function (Team $model) {
            return $model->getStatusName();
        }
    ],
    [
        'class' => '\kartik\grid\ActionColumn',
        'width' => '350px',
        'template' => '{update} {view} {reset-password} {change-status}',
        'buttons' => [
            'update' => function ($url) {
                if (!AuthValidate::has(AdminAuth::TEAM_UPDATE)) {
                    return '';
                }
                $options = [
                    'class' => 'btn btn-default show_ajax_modal',
                ];
                return Html::a('更新', $url, $options);
            },
            'view' => function ($url) {
                $options = [
                    'class' => 'btn btn-default',
                ];
                return Html::a('成员', $url, $options);
            },
            'change-status' => function ($url, Team $model) {
                if (!AuthValidate::has(AdminAuth::TEAM_CHANGE_STATUS)) {
                    return '';
                }
                $options = [
                    'class' => 'btn btn-danger',
                    'data-method' => 'post'
                ];
                if ($model->status == Team::STATUS_NORMAL) {
                    return Html::a('禁用', ['change-status', 'id' => $model->id, 'status' => Team::STATUS_DISABLE], $options);
                } elseif ($model->status == Team::STATUS_DISABLE) {
                    return Html::a('恢复', ['change-status', 'id' => $model->id, 'status' => Team::STATUS_NORMAL], $options);
                }
                return '';
            },
            'reset-password' => function ($url) {
                if (!AuthValidate::has(AdminAuth::TEAM_RESET_PASSWORD)) {
                    return '';
                }
                $options = [
                    'class' => 'btn btn-danger show_ajax_modal',
                ];
                return Html::a('重置管理员密码', $url, $options);
            },
        ],
    ],
];

$simpleDynaGrid = new SimpleDynaGrid([
    'dynaGridId' => 'dynagrid-team-index',
    'columns' => $columns,
    'dataProvider' => $dataProvider,
    'extraToolbar' => [
        [
            'content' => !AuthValidate::has(AdminAuth::TEAM_CREATE) ? '' :
                Html::a('新增', ['create'], ['class' => 'btn btn-default'])
        ]
    ]
]);
$simpleDynaGrid->renderDynaGrid();
