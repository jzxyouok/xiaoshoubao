<?php
/** @var $this yii\web\View */
/** @var $model \common\models\Province */

use common\widgets\SimpleAjaxForm;

$form = SimpleAjaxForm::begin([
    'header' => ($model->isNewRecord) ? '新建' : '修改'
]);

echo $form->field($model, 'name');

SimpleAjaxForm::end();
