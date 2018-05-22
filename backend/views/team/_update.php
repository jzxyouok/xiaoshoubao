<?php
/** @var $this \yii\web\View */
/** @var $model \common\models\Team */

use common\widgets\SimpleAjaxForm;
use kriss\widgets\DateControl;

$form = SimpleAjaxForm::begin(['header' => '修改团队信息']);

echo $form->field($model, 'name');
echo $form->field($model, 'past_at')->widget(DateControl::className(), [
    'type' => DateControl::FORMAT_DATETIME
]);;
echo $form->field($model, 'max_clue_size')->input('number', ['min' => 0]);

$form->end();