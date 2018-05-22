<?php
/** @var $this \yii\web\View */
/** @var $model \common\models\elasticsearch\Company */

use common\models\Record;
use frontend\widgets\SimpleListView;
use yii\data\ActiveDataProvider;

$dataProvider = new ActiveDataProvider([
    'query' => Record::findInTeam()->andWhere(['company_id' => $model->id]),
    'sort' => [
        'defaultOrder' => [
            'created_at' => SORT_DESC,
            'id' => SORT_DESC,
        ],
    ],
]);
echo SimpleListView::widget([
    'dataProvider' => $dataProvider,
    'enablePjax' => true,
    'canJump' => false,
    'itemView' => '_record_item',
    'layout' => "<ul class='timeline'>{items}</ul>\n<div class='text-center'>{pager}</div>",
    'itemOptions' => [
        'tag' => 'li',
    ]
]);