<?php
/** @var $this \yii\web\View */
/** @var $model \common\models\Clue */
/** @var $checkboxName string */

use common\models\base\UserAuth;
use common\models\Clue;
use common\models\UserClue;
use frontend\components\Tools;
use kriss\modules\auth\tools\AuthValidate;
use yii\helpers\Html;
use yii\helpers\Url;

$company = $model->company;
?>
    <div class="box box-solid">
        <div class="box-header with-border clearfix">
            <?php if (AuthValidate::has(UserAuth::CLUE_DISTRIBUTE)) {
                echo Html::checkbox($checkboxName, '', [
                    'data-key' => $company->id,
                ]);
            } ?>

            <?php if (AuthValidate::has(UserAuth::USER_CLUE_PICK)) {
                echo UserClue::isUserPicked($company->id) || Clue::isUserPicked($company->id) ?
                    Html::button('已领取', [
                        'class' => 'btn btn-info btn-sm'
                    ]) :
                    Html::button('领取线索', [
                        'class' => 'btn btn-success btn-sm pick_clue',
                        'data-url' => Url::to(['/user-clue/pick']),
                        'data-company' => $company->id
                    ]);
            } ?>

            <?php if (AuthValidate::has(UserAuth::CLUE_DISTRIBUTE)) {
                echo $model->status == Clue::STATUS_USER_PICKED ?
                    Html::button('已被领取', [
                        'class' => 'btn btn-info btn-sm'
                    ]) :
                    Html::a('分配线索', ['/clue/distribute', 'id' => $model->id], [
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
                    <p><strong>注册资本：</strong><?= $company->registered_capital ?>万元</p>
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

<?php
if (AuthValidate::has(UserAuth::USER_CLUE_PICK)) {
    $js = <<<JS
$('.pick_clue').click(function() {
    var url = $(this).data('url'),
        companyId = $(this).data('company'),
        _this = $(this);
    http.post(url, {company_id: companyId}, function(data) {
        _this.text('已领取').removeClass('btn-success pick_clue').addClass('btn-info');
        var otherOperation = _this.siblings('.show_ajax_modal');
        if(otherOperation){
            otherOperation.text('已被领取').removeClass('btn-success show_ajax_modal').addClass('btn-info');
        }
        alert(data);
    });
});
JS;
    $this->registerJs($js);
}