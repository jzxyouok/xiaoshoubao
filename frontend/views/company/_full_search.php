<?php
/** @var $this \yii\web\View */
/** @var $model \frontend\models\CompanySearch */

use yii\helpers\Html;

?>
<?= Html::beginForm(['full-search'], 'get') ?>

<?= Html::textInput('s', '', ['placeholder' => '请输入']) ?>
<button type="submit" class="btn btn-info btn-flat">Go!</button>

<?= Html::endForm(); ?>

