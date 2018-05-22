<?php
/** @var $this yii\web\View */
/** @var $dataProvider frontend\components\ActiveDataProvider */

use common\models\base\UserAuth;
use common\models\User;
use common\widgets\SimpleDynaGrid;
use kriss\modules\auth\tools\AuthValidate;
use yii\helpers\Html;

$this->title = '帐号管理列表';
$this->params['breadcrumbs'] = [
    '帐号管理',
    $this->title,
];

$columns = [
    [
        'attribute' => 'name',
    ],
    [
        'attribute' => 'cellphone',
    ],
    [
        'attribute' => 'department.name',
    ],
    [
        'attribute' => 'type',
        'value' => function (User $model) {
            return $model->getTypeName();
        }
    ],
    [
        'attribute' => 'status',
        'value' => function (User $model) {
            return $model->getStatusName();
        }
    ],
    [
        'class' => '\kartik\grid\ActionColumn',
        'width' => '300px',
        'template' => '{update} {update-role} {reset-password} {change-status}',
        'buttons' => [
            'update' => function ($url) {
                if (!AuthValidate::has(UserAuth::USER_UPDATE)) {
                    return '';
                }
                $options = [
                    'class' => 'btn btn-default show_ajax_modal',
                ];
                return Html::a('更新', $url, $options);
            },
            'update-role' => function ($url, User $model) {
                if (!AuthValidate::has(UserAuth::USER_UPDATE_ROLE)) {
                    return '';
                }
                if ($model->type != User::TYPE_ADMIN && $model->id != Yii::$app->user->id) {
                    $options = [
                        'class' => 'btn btn-warning show_ajax_modal',
                    ];
                    return Html::a('授权', $url, $options);
                }
                return '';
            },
            'reset-password' => function ($url) {
                if (!AuthValidate::has(UserAuth::USER_RESET_PASSWORD)) {
                    return '';
                }
                $options = [
                    'class' => 'btn btn-danger show_ajax_modal',
                ];
                return Html::a('重置密码', $url, $options);
            },
            'change-status' => function ($url, User $model) {
                if (!AuthValidate::has(UserAuth::USER_CHANGE_STATUS)) {
                    return '';
                }
                // 管理员和自己的状态不可修改
                if ($model->type == User::TYPE_ADMIN || $model->id == Yii::$app->user->id) {
                    return '';
                }
                $options = [
                    'class' => 'btn btn-danger',
                    'data-method' => 'post'
                ];
                if ($model->status == User::STATUS_NORMAL) {
                    return Html::a('禁用', ['change-status', 'id' => $model->id, 'status' => User::STATUS_DISABLE], $options);
                } elseif ($model->status == User::STATUS_DISABLE) {
                    return Html::a('恢复', ['change-status', 'id' => $model->id, 'status' => User::STATUS_NORMAL], $options);
                }
                return '';
            }
        ],
    ],
];

$simpleDynaGrid = new SimpleDynaGrid([
    'dynaGridId' => 'dynagrid-user-index',
    'columns' => $columns,
    'dataProvider' => $dataProvider,
    'extraToolbar' => [
        [
            'content' => !AuthValidate::has(UserAuth::USER_CREATE) ? '' :
                Html::a('新增', ['create'], ['class' => 'btn btn-default show_ajax_modal'])
        ]
    ]
]);
$simpleDynaGrid->renderDynaGrid();
