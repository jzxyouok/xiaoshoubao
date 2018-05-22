<?php
/** @var $this \yii\web\View */
/** @var $model \common\models\elasticsearch\Company */

use common\widgets\DLHorizontal;

echo DLHorizontal::widget([
    'model' => $model,
    'attributes' => [
        'legal_person',
        'leading_member',
        'cellphone',
        'mail',
    ]
]);