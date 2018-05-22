<?php
/** @var $this \yii\web\View */
/** @var $model \common\models\elasticsearch\Company */

$this->title = '企业详情';
$this->params['breadcrumbs'] = [
    $this->title,
];
?>
<div class="row">
    <div class="col-lg-3">
        <div class="box box-primary">
            <div class="box-body box-profile">
                <?= $this->render('_view_profile', [
                    'model' => $model
                ]) ?>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#base-info" data-toggle="tab">企业基本信息</a></li>
                <li><a href="#contact" data-toggle="tab">联系方式</a></li>
                <li><a href="#record" data-toggle="tab">跟进记录</a></li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane row" id="base-info">
                    <?= $this->render('_view_info', [
                        'model' => $model
                    ]) ?>
                </div>
                <div class="tab-pane row" id="contact">
                    <?= $this->render('_view_contact', [
                        'model' => $model
                    ]) ?>
                </div>
                <div class="tab-pane" id="record">
                    <?= $this->render('_view_record', [
                        'model' => $model
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>