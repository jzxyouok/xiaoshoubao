<?php
/** @var $this yii\web\View */
/** @var $dataProvider common\components\ActiveDataProvider */
/** @var $searchModel backend\models\UserSearch */

/** @var $team \common\models\Team */

use common\models\User;
use common\widgets\SimpleDynaGrid;

$this->title = $team->name . '-成员信息';
$this->params['breadcrumbs'] = [
    '团队管理',
    [
        'label' => '团队列表',
        'url' => ['team/index'],
    ],
    $this->title,
];

echo $this->render('_search', [
    'model' => $searchModel,
]);

$columns = [
    [
        'attribute' => 'name',
    ],
    [
        'attribute' => 'cellphone',
    ],
    [
        'attribute' => 'department.name',
    ],
    [
        'attribute' => 'type',
        'value' => function (User $model) {
            return $model->getTypeName();
        }
    ],
    [
        'attribute' => 'status',
        'value' => function (User $model) {
            return $model->getStatusName();
        }
    ],
];

$simpleDynaGrid = new SimpleDynaGrid([
    'dynaGridId' => 'dynagrid-user-index',
    'columns' => $columns,
    'dataProvider' => $dataProvider,
]);
$simpleDynaGrid->renderDynaGrid();
