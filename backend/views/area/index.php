<?php
/** @var $this yii\web\View */
/** @var $dataProvider common\components\ActiveDataProvider */
/** @var $searchModel backend\models\AreaSearch */
/** @var $model \common\models\Area */

use common\widgets\SimpleDynaGrid;
use yii\helpers\Html;

$this->title = '地区管理列表';
$this->params['breadcrumbs'] = [
    '地区管理',
    $this->title,
];

echo $this->render('_search', [
    'model' => $searchModel,
]);

$columns = [
    [
        'attribute' => 'province.name',
        'label' => '省份'
    ],
    [
        'attribute' => 'city.name',
        'label' => '城市'
    ],
    [
        'attribute' => 'name',
    ],
    [
        'class' => '\kartik\grid\ActionColumn',
        'width' => '150px',
        'template' => '{update} {delete}',
        'buttons' => [
            'update' => function ($url) {
                $options = [
                    'class' => 'btn btn-default show_ajax_modal',
                ];
                return Html::a('更新', $url, $options);
            },
            'delete' => function ($url) {
                $options = [
                    'class' => 'btn btn-danger',
                    'data-method' => 'post',
                    'data-confirm' => '确定删除该地区？'
                ];
                return Html::a('删除', $url, $options);
            },
        ],
    ],

];

$simpleDynaGrid = new SimpleDynaGrid([
    'dynaGridId' => 'dynagrid-area-index',
    'columns' => $columns,
    'dataProvider' => $dataProvider,
    'extraToolbar' => [
        [
            'content' => Html::a('新增', ['create'], ['class' => 'btn btn-default show_ajax_modal'])
        ],
    ]
]);
$simpleDynaGrid->renderDynaGrid();
