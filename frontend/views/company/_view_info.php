<?php
/** @var $this \yii\web\View */
/** @var $model \common\models\elasticsearch\Company */

use common\widgets\DLHorizontal;

echo DLHorizontal::widget([
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
        [
            'attribute' => 'registered_capital',
            'value' => $model->registered_capital . '万元'
        ],
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
]);