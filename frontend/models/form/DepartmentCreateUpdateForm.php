<?php

namespace frontend\models\form;

use common\components\Tools;
use common\models\Department;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class DepartmentCreateUpdateForm extends Model
{
    public $name;
    public $userIds;

    /**
     * 更新时传入
     * @var Department
     */
    public $department;

    public function init()
    {
        if (!$this->department) {
            $this->department = new Department();
            $this->department->linkTeam();
        }
        if (!$this->department->isNewRecord) {
            $this->name = $this->department->name;
            $this->userIds = ArrayHelper::getColumn($this->department->users, 'id');
        }
    }

    public function rules()
    {
        return [
            ['name', 'required'],
            ['userIds', 'each', 'rule' => ['integer']]
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => '部门名称',
            'userIds' => '成员',
        ];
    }

    /**
     * 保存信息
     * @return bool
     */
    public function save()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 更新部门信息
            $this->department->name = $this->name;
            if (!$this->department->validate()) {
                $this->addError('name', Tools::getFirstError($this->department->errors));
                return false;
            }
            $this->department->save(false);
            // 更新成员的部门
            /** @var User $user */
            $user = Yii::$app->user->identity;
            // 先把原用户置空
            User::updateAll(['department_id' => 0], ['team_id' => $user->team_id, 'department_id' => $this->department->id]);
            // 设置新的部门
            if ($this->userIds) {
                User::updateAll(['department_id' => $this->department->id], ['team_id' => $user->team_id, 'id' => $this->userIds]);
            }
            $transaction->commit();
            return true;
        } catch (\Exception $exception) {
            $transaction->rollBack();
            $this->addError('xxx', $exception->getMessage());
            return false;
        }
    }
}