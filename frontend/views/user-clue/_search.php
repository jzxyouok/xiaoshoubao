<?php
/** @var $this yii\web\view */
/** @var $model frontend\models\UserClueSearch */

use common\widgets\SimpleSearchForm;

$form = SimpleSearchForm::begin(['action' => ['index']]);

echo $form->field($model, 'company.name');

echo $form->renderFooterButtons();

$form->end();
