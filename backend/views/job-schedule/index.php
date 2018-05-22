<?php
/** @var $this yii\web\View */
/** @var $dataProvider common\components\ActiveDataProvider */
/** @var $searchModel backend\models\JobScheduleSearch */

use common\models\JobSchedule;
use common\widgets\SimpleDynaGrid;

$this->title = '任务管理列表';
$this->params['breadcrumbs'] = [
    '任务管理',
    $this->title,
];

echo $this->render('_search', [
    'model' => $searchModel,
]);

$columns = [
    [
        'attribute' => 'job_name',
    ],
    [
        'attribute' => 'schedule_percent',
        'format' => 'raw',
        'value' => function (JobSchedule $model) {
            return floatval($model->schedule_percent) . '%';
        }
    ],
    [
        'attribute' => 'current_description',
        'width' => '20%'
    ],
    [
        'attribute' => 'status',
        'value' => function (JobSchedule $model) {
            return $model->getStatusName();
        }
    ],
    [
        'attribute' => 'created_at',
        'format' => ['datetime', 'php:Y-m-d H:i:s']
    ],
    [
        'attribute' => 'updated_at',
        'format' => ['datetime', 'php:Y-m-d H:i:s']
    ],
];

$simpleDynaGrid = new SimpleDynaGrid([
    'dynaGridId' => 'dynagrid-job-schedule-index',
    'columns' => $columns,
    'dataProvider' => $dataProvider,
]);
$simpleDynaGrid->renderDynaGrid();
