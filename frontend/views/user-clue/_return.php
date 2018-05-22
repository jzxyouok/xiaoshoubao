<?php
/** @var $model \frontend\models\form\UserCluePickForm */

use common\widgets\SimpleAjaxForm;

$form = SimpleAjaxForm::begin([
    'header' => '退回线索'
]);

echo $form->field($model, 'remark')->textarea([
    'rows' => 5
]);

SimpleAjaxForm::end();