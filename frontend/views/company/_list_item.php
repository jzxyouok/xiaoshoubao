<?php
/** @var $this \yii\web\View */
/** @var $model \common\models\elasticsearch\Company */

use common\models\base\UserAuth;
use common\models\Clue;
use kriss\modules\auth\tools\AuthValidate;
use yii\helpers\Html;
use yii\helpers\Url;

?>
    <div class="box box-solid">
        <div class="box-body">
            <div class="media">
                <div class="media-left media-middle">
                    <?= Html::img('@web/images/company_default.jpg', ['style' => 'width:100px']) ?>
                </div>
                <div class="media-body highlight-container">
                    <h3>
                        <?= Html::a($model->getHighlightAttribute('name'), ['/company/view', 'id' => $model->id]) ?>
                        <small class="badge bg-aqua"><?= $model->type_name ?></small>
                        <small class="badge bg-aqua"><?= $model->type_business ?></small>
                        <small class="badge bg-aqua"><?= $model->business_status ?></small>
                    </h3>
                    <p>
                        <strong>公司法人</strong>：<?= $model->getHighlightAttribute('legal_person'); ?>
                        <strong>注册资本</strong>：<?= $model->registered_capital ?>万元
                        <strong>成立日期</strong>：<?= $model->establishment_date ?>
                    </p>
                    <p>
                        <strong>地址</strong>：<?= $model->getHighlightAttribute('address'); ?>
                    </p>
                    <p>
                        <strong>经营范围</strong>：<?= $model->getHighlightAttribute('business_scope'); ?>
                    </p>
                </div>
                <div class="media-right media-middle">
                    <p>
                        <?php if (AuthValidate::has(UserAuth::CLUE_PICK)) {
                            echo Clue::isTeamPicked($model->id) ?
                                Html::button('已领取', [
                                    'class' => 'btn btn-info'
                                ]) :
                                Html::button('领取线索', [
                                    'class' => 'btn btn-success pick_clue',
                                    'data-url' => Url::to(['/clue/pick']),
                                    'data-company' => $model->id
                                ]);
                        } ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

<?php
if (AuthValidate::has(UserAuth::CLUE_PICK)) {
    $js = <<<JS
$('.pick_clue').click(function() {
    var url = $(this).data('url'),
        companyId = $(this).data('company'),
        _this = $(this);
    http.post(url, {company_id: companyId}, function(data) {
        _this.text('已领取').removeClass('btn-success pick-clue').addClass('btn-info');
        alert(data);
    });
});
JS;
    $this->registerJs($js);
}
