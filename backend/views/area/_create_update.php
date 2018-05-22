<?php
/** @var $this yii\web\View */

/** @var $model \common\models\Area */

use common\widgets\SimpleAjaxForm;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use common\models\Province;
use common\models\City;

$city = City::findIdNames($model->province_id, true);
$form = SimpleAjaxForm::begin(['header' => ($model->isNewRecord ? '创建' : '更新') . '地区']);

echo $form->field($model, 'province_id')->dropDownList(Province::findIdNames(true), [
    'id' => 'province_id',
    'data' => Province::findIdNames(true),
    'prompt' => '请选择'
])->label('省份');


echo $form->field($model, 'city_id')->widget(DepDrop::classname(), [
    'options' => ['id' => 'city_id', 'prompt' => '请选择',],
    'data' => $city,
    'pluginOptions' => [
        'depends' => ['province_id'],
        'placeholder' => 'Select...',

        'url' => Url::to(['/depend/city'])
    ]
])->label('城市');

echo $form->field($model, 'name');

SimpleAjaxForm::end();