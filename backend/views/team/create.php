<?php
/** @var $this \yii\web\View */
/** @var $team \common\models\Team */
/** @var $user \common\models\User */

use common\widgets\SimpleActiveForm;
use kriss\widgets\DateControl;
use yii\helpers\Html;

$this->title = ($team->isNewRecord ? '创建' : '更新') . '团队信息';
$this->params['breadcrumbs'] = [
    '团队管理',
    [
        'label' => '团队列表',
        'url' => ['index']
    ],
    $this->title,
];

$form = SimpleActiveForm::begin([
    'title' => $this->title
]);

echo $form->field($team, 'name');
echo $form->field($team, 'past_at')->widget(DateControl::className(), [
    'type' => DateControl::FORMAT_DATETIME
]);
echo $form->field($team, 'max_clue_size')->input('number', ['min' => 0]);

echo Html::beginTag('div', ['class' => 'row']);
echo Html::tag('p', '团队管理员信息', ['class' => 'text-center col-sm-offset-2 col-sm-5']);
echo Html::endTag('div');

echo $form->field($user, 'name');
echo $form->field($user, 'cellphone');
echo $form->field($user, 'password_hash')->label('密码')->passwordInput();
echo $form->field($user, 'max_clue_size')->input('number', ['min' => 0]);

echo $form->renderFooterButtons();

SimpleActiveForm::end();
