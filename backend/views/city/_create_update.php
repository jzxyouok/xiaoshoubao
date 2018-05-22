<?php
/** @var $this yii\web\View */
/** @var $model \common\models\Province */

use common\widgets\SimpleAjaxForm;
use common\models\Province;

$form = SimpleAjaxForm::begin(['header'=>($model->isNewRecord) ? '新建':'修改']);

    echo $form->field($model,'province_id')->dropDownList(Province::findIdNames(true),[
        'id' => 'id',
        'data' => Province::findIdNames(true),
        'prompt'=>'请选择省份'
    ])->label('省名称');

    echo $form->field($model,'name');
SimpleAjaxForm::end();
