<?php
/** @var $this yii\web\View */
/** @var $dataProvider frontend\components\ActiveDataProvider */

use common\models\base\UserAuth;
use common\models\Department;
use common\widgets\SimpleDynaGrid;
use kriss\modules\auth\tools\AuthValidate;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$this->title = '部门管理列表';
$this->params['breadcrumbs'] = [
    '部门管理',
    $this->title,
];

$columns = [
    [
        'attribute' => 'name',
    ],
    [
        'label' => '成员',
        'value' => function (Department $model) {
            return implode('<br>', ArrayHelper::getColumn($model->users, 'name'));// 连在一起的另一张表取name，然后因为是一对多出来是数组，用implode
        },
        'format' => 'html'
    ],
    [
        'class' => '\kartik\grid\ActionColumn',
        'width' => '150px',
        'template' => '{update} {delete}',
        'buttons' => [
            'update' => function ($url) {
                if (!AuthValidate::has(UserAuth::DEPARTMENT_UPDATE)) {
                    return '';
                }
                $options = [
                    'class' => 'btn btn-default show_ajax_modal',
                ];
                return Html::a('更新', $url, $options);
            },
            'delete' => function ($url) {
                if (!AuthValidate::has(UserAuth::DEPARTMENT_DELETE)) {
                    return '';
                }
                $options = [
                    'class' => 'btn btn-danger',
                    'data-confirm' => '确认删除？删除后原该部门下的所有用户的"部门信息"将被清空',
                    'data-method' => 'post'
                ];
                return Html::a('删除', $url, $options);
            },
        ],
    ],
];

$simpleDynaGrid = new SimpleDynaGrid([
    'dynaGridId' => 'dynagrid-department-index',
    'columns' => $columns,
    'dataProvider' => $dataProvider,
    'extraToolbar' => [
        [
            'content' => !AuthValidate::has(UserAuth::DEPARTMENT_CREATE) ? '' :
                Html::a('新增', ['create'], ['class' => 'btn btn-default show_ajax_modal'])
        ]
    ]
]);
$simpleDynaGrid->renderDynaGrid();
