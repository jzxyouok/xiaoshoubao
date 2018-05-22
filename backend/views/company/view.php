<?php
/** @var $this \yii\web\View */
/** @var $model \common\models\elasticsearch\Company */

use common\widgets\DLHorizontal;

$this->title = '企业信息详情';
$this->params['breadcrumbs'] = [
    '企业信息管理',
    [
        'label' => '企业信息管理列表',
        'url' => ['index'],
    ],
    $this->title,
];
?>
<div class="box">
    <div class="box-body">
        <?= DLHorizontal::widget([
            'model' => $model,
            'attributes' => [
                'name',
                'registration_number',
                'type_code',
                'type_name',
                'type_business',
                'social_credit_code',
                'organization_code',
                'legal_person',
                'establishment_date',
                'registered_capital',
                'business_scope',
                'shareholder_information',
                'leading_member',
                'cellphone',
                'mail',
                'address',
                'website',
                'branch',
                'business_status',
                'history_name',
                'province',
                'business_term',
                'issue_date',
                'registration_authority',
                'change_record',
            ]
        ]); ?>
    </div>
</div>
