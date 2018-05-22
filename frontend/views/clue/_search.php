<?php
/** @var $this yii\web\view */
/** @var $model frontend\models\ClueSearch */

use common\models\Clue;
use common\widgets\SimpleSearchForm;

$form = SimpleSearchForm::begin(['action' => ['index']]);

echo $form->field($model, 'company.name');
echo $form->field($model, 'status')->dropDownList(Clue::$statusData, [
    'prompt' => '全部'
]);

echo $form->renderFooterButtons();

$form->end();
