<?php
/** @var $this yii\web\view */
/** @var $model backend\models\CitySearch */

use common\widgets\SimpleSearchForm;

$form = SimpleSearchForm::begin(['action' => ['index']]);

echo $form->field($model, 'name');

echo $form->renderFooterButtons();
$form->end();
