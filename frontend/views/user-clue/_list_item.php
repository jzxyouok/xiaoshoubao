<?php
/** @var $this \yii\web\View */
/** @var $model \common\models\UserClue */
/** @var $checkboxName string */

use common\models\base\UserAuth;
use frontend\components\Tools;
use kriss\modules\auth\tools\AuthValidate;
use yii\helpers\Html;

$company = $model->company;
?>
<div class="box box-solid">
    <div class="box-header with-border clearfix">
        <?php if (AuthValidate::has(UserAuth::USER_CLUE_RETURN)) {
            echo Html::a('退回线索', ['/user-clue/return', 'id' => $model->id], [
                'class' => 'btn btn-success btn-sm show_ajax_modal',
            ]);
        } ?>

        <?php if (AuthValidate::has(UserAuth::USER_CLUE_RECORD)) {
            echo Html::a('线索跟进', ['/user-clue/record', 'id' => $model->id], [
                'class' => 'btn btn-success btn-sm show_ajax_modal',
            ]);
        } ?>

        <small class="text-muted pull-right">线索更新时间：<?= date('Y-m-d H:i:s', $model->updated_at) ?></small>
    </div>
    <div class="box-body">
        <div class="media">
            <div class="media-left media-middle">
                <?= Html::img('@web/images/company_default.jpg', ['style' => 'width:100px']) ?>
            </div>
            <div class="media-body">
                <h3><?= Html::a($company->name, ['/company/view', 'id' => $company->id]) ?></h3>
                <p>
                    <small class="badge bg-aqua"><?= $company->type_name ?></small>
                    <small class="badge bg-aqua"><?= $company->type_business ?></small>
                    <small class="badge bg-aqua"><?= $company->business_status ?></small>
                </p>
                <p><strong>公司法人：</strong><?= $company->legal_person ?></p>
                <p><strong>注册资本：</strong><?= $company->registered_capital ?></p>万元
                <p><strong>成立日期：</strong><?= $company->establishment_date ?></p>
                <?php $website = $company->website ? Tools::urlAddSchema($company->website) : '' ?>
                <p><strong>公司网站：</strong><?= $website ? Html::a($website, $website) : '-' ?></p>
                <p><strong>主要成员：</strong><?= $company->leading_member ?: '-' ?></p>
                <p><strong>电话：</strong><?= $company->cellphone ?: '-' ?></p>
                <p><strong>邮箱：</strong><?= $company->mail ?: '-' ?></p>
                <p><strong>地址：</strong><?= $company->address ?></p>
            </div>
        </div>
    </div>
</div>