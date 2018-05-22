<?php
/** @var $this yii\web\View */
/** @var $dataProvider common\components\ActiveDataProvider */
/** @var $searchModel backend\models\CompanyUploadSearch */
/** @var $canSync bool */

use common\models\base\AdminAuth;
use common\widgets\SimpleDynaGrid;
use kriss\modules\auth\tools\AuthValidate;
use yii\helpers\Html;

$this->title = '企业信息上传管理列表';
$this->params['breadcrumbs'] = [
    '企业信息上传管理',
    $this->title,
];

echo $this->render('_search', [
    'model' => $searchModel,
]);

$columns = [
    [
        'attribute' => 'name',
        'enableSorting' => false,
    ],
    [
        'attribute' => 'registration_number',
        'enableSorting' => false,
    ],
    [
        'attribute' => 'social_credit_code',
        'enableSorting' => false,
    ],
    [
        'attribute' => 'organization_code',
        'enableSorting' => false,
    ],
    [
        'attribute' => 'legal_person',
        'enableSorting' => false,
    ],
    [
        'class' => '\kartik\grid\ActionColumn',
        'width' => '250px',
        'template' => '{update} {view} {delete}',
        'buttons' => [
            'update' => function ($url) {
                if (!AuthValidate::has(AdminAuth::COMPANY_UPLOAD_UPDATE)) {
                    return '';
                }
                $options = [
                    'class' => 'btn btn-default',
                ];
                return Html::a('更新', $url, $options);
            },
            'view' => function ($url) {
                $options = [
                    'class' => 'btn btn-default',
                ];
                return Html::a('详情', $url, $options);
            },
            'delete' => function ($url) {
                if (!AuthValidate::has(AdminAuth::COMPANY_UPLOAD_DELETE)) {
                    return '';
                }
                $options = [
                    'class' => 'btn btn-danger',
                    'data-method' => 'post',
                    'data-confirm' => '确认删除？'
                ];
                return Html::a('删除', $url, $options);
            },
        ],
    ],
];

$simpleDynaGrid = new SimpleDynaGrid([
    'dynaGridId' => 'dynagrid-company-upload-index',
    'columns' => $columns,
    'dataProvider' => $dataProvider,
    'extraToolbar' => [
        [
            'content' => !AuthValidate::has(AdminAuth::COMPANY_UPLOAD_IMPORT) ? '' :
                Html::a('导入', ['import'], ['class' => 'btn btn-success'])
        ],
        [
            'content' => !AuthValidate::has(AdminAuth::COMPANY_UPLOAD_EMPTY) ? '' :
                Html::a('清空', ['empty'], [
                    'class' => 'btn btn-danger',
                    'data-method' => 'post',
                    'data-confirm' => '确认清空数据？'
                ])
        ],
        [
            'content' => !$canSync ? '' :
                Html::a('同步到正式库', ['sync'], [
                    'class' => 'btn btn-success',
                    'data-method' => 'post',
                    'data-confirm' => '确认同步到正式库？'
                ])
        ],
    ]
]);
$simpleDynaGrid->renderDynaGrid();
