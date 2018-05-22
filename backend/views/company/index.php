<?php
/** @var $this yii\web\View */
/** @var $dataProvider common\components\ActiveDataProvider */
/** @var $searchModel backend\models\CompanySearch */
/** @var $canSync bool */

use common\models\base\AdminAuth;
use common\models\Company;
use common\widgets\SimpleDynaGrid;
use kriss\modules\auth\tools\AuthValidate;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = '企业信息管理列表';
$this->params['breadcrumbs'] = [
    '企业信息管理',
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
        'template' => '{view} {update} {change-status} {delete}',
        'buttons' => [
            'view' => function ($url) {
                $options = [
                    'class' => 'btn btn-default',
                ];
                return Html::a('详情', $url, $options);
            },
            'update' => function ($url) {
                if (!AuthValidate::has(AdminAuth::COMPANY_UPDATE)) {
                    return '';
                }
                $options = [
                    'class' => 'btn btn-default',
                ];
                return Html::a('更新', $url, $options);
            },
            'change-status' => function ($url, Company $model) {
                if (!AuthValidate::has(AdminAuth::COMPANY_CHANGE_STATUS)) {
                    return '';
                }
                $options = [
                    'class' => 'btn btn-danger',
                    'data-method' => 'post'
                ];
                if ($model->status == Company::STATUS_NORMAL) {
                    return Html::a('禁用', ['change-status', 'id' => $model->id, 'status' => Company::STATUS_DISABLE], $options);
                } elseif ($model->status == Company::STATUS_DISABLE) {
                    return Html::a('恢复', ['change-status', 'id' => $model->id, 'status' => Company::STATUS_NORMAL], $options);
                }
                return '';
            },
            'delete' => function ($url) {
                if (!AuthValidate::has(AdminAuth::COMPANY_DELETE)) {
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
    'dynaGridId' => 'dynagrid-company-index',
    'columns' => $columns,
    'dataProvider' => $dataProvider,
    'extraToolbar' => [
        [
            'content' => !AuthValidate::has(AdminAuth::COMPANY_CREATE) ? '' :
                Html::a('新增', ['create'], ['class' => 'btn btn-default'])
        ],
        [
            'content' => !$canSync ? '' :
                Html::a('同步到查询库', ['sync'], [
                    'class' => 'btn btn-success',
                    'data-method' => 'post',
                    'data-confirm' => '确认同步到查询库？'
                ])
        ]
    ],
    'gridPager' => [
        'class' => LinkPager::className(),
    ]
]);
$simpleDynaGrid->renderDynaGrid();
