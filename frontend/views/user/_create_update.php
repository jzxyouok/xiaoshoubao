<?php
/** @var $this \yii\web\View */
/** @var $model \common\models\Department */

use common\widgets\SimpleAjaxForm;
use common\models\Department;
use yii\helpers\ArrayHelper;

$departments = ArrayHelper::map(Department::findInTeam()->select(['id', 'name'])->asArray()->all(), 'id', 'name');

$form = SimpleAjaxForm::begin(['header' => ($model->isNewRecord ? '创建' : '更新') . '帐号']);

echo $form->field($model, 'name');
echo $form->field($model, 'cellphone');
echo $form->field($model, 'max_clue_size');
if ($model->isNewRecord) {
    echo $form->field($model, 'password_hash')->passwordInput();
}
echo $form->field($model, 'department_id')->dropDownList($departments, [
    'prompt' => '请选择（选填）'
]);

$form->end();