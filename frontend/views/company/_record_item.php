<?php
/** @var $this \yii\web\View */
/** @var $model Record */

use common\models\Record;

?>
<?php if ($model->type == Record::TYPE_SYSTEM): ?>
    <i class="fa fa-fw fa-cog bg-aqua"></i>
<?php else: ?>
    <i class="fa fa-fw fa-file-text bg-blue"></i>
<?php endif; ?>
<div class="timeline-item">
    <span class="time">
        <i class="fa fa-clock-o"></i>
        <?= date('Y-m-d H:i:s', $model->created_at) ?>
    </span>
    <h3 class="timeline-header no-border">
        <?= $model->getFormatContent() ?>
    </h3>
</div>
