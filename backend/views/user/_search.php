<?php
/** @var $this yii\web\view */
/** @var $model backend\models\UserSearch */

use common\widgets\SimpleSearchForm;

$form = SimpleSearchForm::begin(['action' => ['index']]);

echo $form->field($model, 'name');
echo $form->field($model, 'cellphone');

echo $form->renderFooterButtons();

$form->end();
