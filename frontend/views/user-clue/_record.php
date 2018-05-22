<?php
/** @var $model \frontend\models\form\UserCluePickForm */

use common\models\Record;
use common\models\User;
use common\widgets\SimpleAjaxForm;
use yii\helpers\ArrayHelper;

$users = User::findInTeam()->select(['id', 'name'])->asArray()->all();
$userIdNames = ArrayHelper::map($users, 'id', 'name');

$form = SimpleAjaxForm::begin([
    'header' => '跟进记录'
]);

echo $form->field($model, 'type')->dropDownList(Record::$typeOptionDataUserCanSee, [
    'prompt' => '请选择'
]);
echo $form->field($model, 'content')->textarea([
    'rows' => 5
]);

SimpleAjaxForm::end();