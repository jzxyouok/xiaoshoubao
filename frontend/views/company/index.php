<?php
/** @var $model \common\models\Company */
/** @var $dataProvider */
/** @var $searchModel \frontend\models\CompanySearch */

use frontend\components\Tools;
use frontend\widgets\SimpleListView;
use yii\helpers\Html;

$this->title = '企业查询';
$this->params['breadcrumbs'] = [
    $this->title,
];

echo $this->render('_search', [
    'model' => $searchModel,
]);

echo Html::tag('hr');

if ($searchModel->s) {
    $totalCount = $dataProvider->getTotalCount();
    if ($totalCount) {
        echo Html::tag('div',
            '为您查询到' . Html::tag('strong', Tools::totalCountAbout($totalCount), ['class' => 'text-red']) . '家企业',
            ['class' => 'box-header']
        );
    }
    echo SimpleListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_list_item',
        'layout' => "{items}\n<div class='text-center'>{pager}</div>",
        'canJump' => false
    ]);
}
