<?php
/** @var $this \yii\web\View */
/** @var $model Company */

use common\models\Company;
use common\widgets\SimpleActiveForm;

$registeredCapitalUnit = Company::REGISTERED_CAPITAL_UNIT;

$form = SimpleActiveForm::begin();

echo $form->field($model, 'name');
echo $form->field($model, 'registration_number');
echo $form->field($model, 'type_code');
echo $form->field($model, 'type_name');
echo $form->field($model, 'type_business');
echo $form->field($model, 'social_credit_code');
echo $form->field($model, 'organization_code');
echo $form->field($model, 'legal_person');
echo $form->field($model, 'establishment_date')->input('text', ['placeholder' => '请按照 XXXX-XX-XX 的格式填写']);

$model->registered_capital = floatval($model->registered_capital);
echo $form->field($model, 'registered_capital', [
    'template' => "{label}\n<div class='col-sm-5 input-group' style='padding-left: 15px;padding-right: 15px;float: left'>{input}
<span class='input-group-addon'>{$registeredCapitalUnit}</span></div>\n{hint}\n{error}"
])->input('number', ['min' => 0]);

echo $form->field($model, 'business_scope');
echo $form->field($model, 'shareholder_information');
echo $form->field($model, 'leading_member');
echo $form->field($model, 'cellphone');
echo $form->field($model, 'mail');
echo $form->field($model, 'address');
echo $form->field($model, 'website');
echo $form->field($model, 'branch');
echo $form->field($model, 'business_status');
echo $form->field($model, 'history_name');
echo $form->field($model, 'province');
echo $form->field($model, 'business_term');
echo $form->field($model, 'issue_date')->input('text', ['placeholder' => '请按照 XXXX年XX月XX日 的格式填写']);
echo $form->field($model, 'registration_authority')->input('text', ['placeholder' => '请按照 XXXX年XX月XX日 的格式填写']);
echo $form->field($model, 'change_record');

echo $form->renderFooterButtons();

SimpleActiveForm::end();


