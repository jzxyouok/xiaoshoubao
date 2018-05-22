<?php
/** @var $this \yii\web\View */
/** @var $model \common\models\elasticsearch\Company */

use common\models\base\UserAuth;
use common\models\Clue;
use common\models\UserClue;
use frontend\components\Tools;
use kriss\modules\auth\tools\AuthValidate;
use yii\helpers\Html;
use yii\helpers\Url;

$clue = Clue::findInTeam()->andWhere(['company_id' => $model->id])->one();
$userClue = UserClue::findInTeam()->andWhere(['company_id' => $model->id, 'status' => UserClue::STATUS_NORMAL])->one();
?>
<?= Html::img('@web/images/company_default.jpg', ['class' => 'profile-user-img img-responsive img-circle']) ?>
    <h3 class="profile-username text-center"><?= $model->name ?></h3>
    <ul class="list-group list-group-unbordered">
        <li class="list-group-item">
            <?php $website = $model->website ? Tools::urlAddSchema($model->website) : '' ?>
            <strong>网站</strong>
            <a class="pull-right"><?= $website ? Html::a($website, $website, ['class' => 'pull-right']) : '-' ?></a>
        </li>
        <li class="list-group-item">
            <strong>法人</strong>
            <span class="pull-right"><?= $model->legal_person ?></span>
        </li>
        <li class="list-group-item">
            <strong>线索状态</strong>
            <span class="label bg-green pull-right"><?= $clue ? $clue->statusName : '未领取' ?></span>
        </li>
        <li class="list-group-item">
            <strong>跟进销售</strong>
            <span class="pull-right"><?= $userClue ? $userClue->user->name : '-' ?></span>
        </li>
    </ul>
<?php
if (!$clue) {
    // 线索未被领取到公海
    if (AuthValidate::has(UserAuth::CLUE_PICK)) {
        echo Html::button('领取线索（到公海）', [
            'class' => 'btn btn-success btn-block pick_clue',
            'data-url' => Url::to(['/clue/pick']),
            'data-company' => $model->id
        ]);
    }
} elseif ($clue->status == Clue::STATUS_NORMAL) {
    // 线索已在公海未被分配和领取
    if (AuthValidate::has(UserAuth::USER_CLUE_PICK)) {
        echo Html::button('领取线索（到私海）', [
            'class' => 'btn btn-success btn-block pick_clue',
            'data-url' => Url::to(['/user-clue/pick']),
            'data-company' => $model->id
        ]);
    }
    if (AuthValidate::has(UserAuth::CLUE_DISTRIBUTE)) {
        echo Html::a('分配线索', ['/clue/distribute', 'id' => $clue->id, 'redirect' => 0], [
            'class' => 'btn btn-success btn-block show_ajax_modal',
        ]);
    }
} else {
    // 线索已被领取到私海
    if ($userClue && $userClue->user_id == Tools::getCurrentUserId()) {
        // 线索是当前用户所有
        if (AuthValidate::has(UserAuth::USER_CLUE_RECORD)) {
            echo Html::a('跟进', ['/user-clue/record', 'id' => $userClue->id, 'redirect' => 0], [
                'class' => 'btn btn-success btn-block show_ajax_modal',
            ]);
        }
        if (AuthValidate::has(UserAuth::USER_CLUE_RETURN)) {
            echo Html::a('退回', ['/user-clue/return', 'id' => $userClue->id, 'redirect' => 0], [
                'class' => 'btn btn-warning btn-block show_ajax_modal',
            ]);
        }
    }
}

if (AuthValidate::has([UserAuth::CLUE_PICK, UserAuth::USER_CLUE_PICK])) {
    $js = <<<JS
$('.pick_clue').on('click', function() {
    var url = $(this).data('url'),
        companyId = $(this).data('company');
    http.post(url, {company_id: companyId}, function() {
        window.location.reload();
    });
});
JS;
    $this->registerJs($js);
}