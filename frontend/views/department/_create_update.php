<?php
/** @var $this \yii\web\View */
/** @var $model \frontend\models\form\DepartmentCreateUpdateForm */

use common\widgets\SimpleAjaxForm;
use common\models\User;
use yii\helpers\ArrayHelper;

// 选出不在其他部门的所有用户
$inDepartmentIds = [0];
if (!$model->department->isNewRecord) {
    array_push($inDepartmentIds, $model->department->id);
}
$users = ArrayHelper::map(
    User::findInTeam()->andWhere(['department_id' => $inDepartmentIds])->select(['id', 'name'])->asArray()->all()
    , 'id'
    , 'name'
);

$form = SimpleAjaxForm::begin(['header' => ($model->department->isNewRecord ? '创建' : '更新') . '部门']);

echo $form->field($model, 'name');
echo $form->field($model, 'userIds')->checkboxList($users);

$form->end();