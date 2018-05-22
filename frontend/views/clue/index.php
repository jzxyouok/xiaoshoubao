<?php
/** @var $this yii\web\View */
/** @var $searchModel */
/** @var $dataProvider */

use common\models\base\UserAuth;
use frontend\widgets\SimpleListView;
use kriss\modules\auth\tools\AuthValidate;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '线索公海池列表';
$this->params['breadcrumbs'] = [
    '线索公海池',
    $this->title,
];

echo $this->render('_search', [
    'model' => $searchModel,
]);

$checkboxName = 'company_ids[]';
?>
<?php if (AuthValidate::has(UserAuth::CLUE_DISTRIBUTE)): ?>
    <div class="box box-solid no-margin-bottom">
        <div class="box-body">
            <?= Html::checkbox('select_all', '', [
                'data-checkbox-name' => $checkboxName
            ]) ?>
            <?= Html::button('批量分配', [
                'class' => 'btn btn-success check_operate_modal',
                'data-url' => Url::to(['/clue/distribute-multi']),
                'data-checkbox-name' => $checkboxName,
            ]) ?>
        </div>
    </div>
<?php endif; ?>
<?php
echo SimpleListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_list_item',
    'viewParams' => [
        'checkboxName' => $checkboxName
    ],
    'layout' => "<div class='col-lg-12'><div class='box-header'>{summary}</div></div>\n{items}\n<div class='text-center'>{pager}</div>",
    'options' => [
        'class' => 'row'
    ],
    'itemOptions' => [
        'class' => 'col-lg-6'
    ]
]);
