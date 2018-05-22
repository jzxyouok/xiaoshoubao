<?php
/** @var $this yii\web\View */
/** @var $dataProvider frontend\components\ActiveDataProvider */
/** @var $searchModel frontend\models\UserClueSearch */

use frontend\widgets\SimpleListView;

$this->title = '线索跟踪列表';
$this->params['breadcrumbs'] = [
    '线索跟踪',
    $this->title,
];

echo $this->render('_search', [
    'model' => $searchModel,
]);

echo SimpleListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_list_item',
    'layout' => "<div class='col-lg-12'><div class='box-header'>{summary}</div></div>\n{items}\n<div class='text-center'>{pager}</div>",
    'options' => [
        'class' => 'row'
    ],
    'itemOptions' => [
        'class' => 'col-lg-6'
    ]
]);
