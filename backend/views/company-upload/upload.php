<?php
/** @var $this \yii\web\View */

/** @var $model \backend\models\form\CompanyUploadForm */

use console\jobs\CompanyImportJob;
use trntv\filekit\widget\Upload;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '导入企业信息';
$this->params['breadcrumbs'] = [
    '企业信息导入管理',
    $this->title,
];
?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">导入企业信息</h3>
    </div>
    <div class="box-body">
        <a href="<?= Url::to(['download-template']) ?>" class="btn btn-primary pull-right">模版下载(企业信息)</a>
        <?php
        $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
        ]);
        echo $form->field($model, 'filename')->widget(Upload::className(), [
            'url' => ['/file/upload'],
            'multiple' => false,
            'maxNumberOfFiles' => 1,
            'maxFileSize' => 20 * 1048576 // 20M
        ]);
        echo Html::submitButton('完成', ['class' => 'btn btn-primary']);
        ActiveForm::end();
        ?>
    </div>
    <div class="box-footer">
        <div class="alert alert-default">
            <p>导入说明：</p>
            <?php
            $messages = [
                '上传文件最大 <span class="text-red">20M</span>',
                '请点击右上方模板下载,按照模板里面提供的格式填写（保证标题栏字段顺序一致）',
                '请不要修改模板的第一栏的标题信息，按照模板的标题对应填写Excel',
                '确保 Excel 列标题字段为（且确保顺序一致）：<br>'
                . Html::tag('span', implode('、', array_values(CompanyImportJob::getImportAttributesMaps())), ['class' => 'text-red']),
            ];
            foreach ($messages as $i => $message) {
                echo Html::tag('p', ($i + 1) . '、' . $message);
            }
            ?>
        </div>
    </div>
</div>
