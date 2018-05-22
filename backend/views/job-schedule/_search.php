<?php
/** @var $this yii\web\view */
/** @var $model backend\models\JobScheduleSearch */

use common\widgets\SimpleSearchForm;

$form = SimpleSearchForm::begin(['action' => ['index']]);

echo $form->field($model, 'job_name');
echo $form->field($model, 'status')->dropDownList(\common\models\JobSchedule::$statusData, [
    'prompt' => '全部'
]);

echo $form->renderFooterButtons();

$form->end();
