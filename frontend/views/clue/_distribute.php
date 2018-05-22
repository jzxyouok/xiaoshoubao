<?php
/** @var $this \yii\web\View */
/** @var $model \frontend\models\form\UserCluePickForm */
/** @var $keys array */

use common\models\User;
use common\widgets\SimpleAjaxForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$users = User::findInTeam()->select(['id', 'name'])->asArray()->all();
$userIdNames = ArrayHelper::map($users, 'id', 'name');

$form = SimpleAjaxForm::begin([
    'header' => '分配线索'
]);

echo Html::activeHiddenInput($model, 'company_ids');
echo $form->field($model, 'user_id')->radioList($userIdNames);

SimpleAjaxForm::end();