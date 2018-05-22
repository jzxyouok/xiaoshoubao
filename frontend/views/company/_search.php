<?php
/** @var $this \yii\web\View */
/** @var $model \frontend\models\CompanySearch */

use yii\helpers\Html;

?>
<?= Html::beginForm(['index'], 'get') ?>
<div class="input-group input-group-lg col-md-10 col-md-offset-1" style="<?= !$model->s ? 'margin-top:200px' : '' ?>">
    <?= Html::textInput('s', $model->s, [
        'class' => 'form-control',
        'placeholder' => '请输入企业名称、公司法人、经营范围、地址等关键字，空格间隔多个关键字'
    ]) ?>
    <span class="input-group-btn">
      <button type="submit" class="btn btn-success">搜索</button>
    </span>
</div>
<?= Html::endForm(); ?>
