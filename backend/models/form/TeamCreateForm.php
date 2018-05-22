<?php

namespace backend\models\form;

use common\models\LogOperation;
use common\models\Team;
use common\models\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

class TeamCreateForm extends Model
{
    /**
     * @var Team
     */
    public $team;
    /**
     * @var User
     */
    public $user;

    public function init()
    {
        if (!$this->team || !$this->team instanceof Team) {
            throw new InvalidConfigException('team 必须配置，且为 common\models\Team 实例');
        }
        if (!$this->user || !$this->user instanceof User) {
            throw new InvalidConfigException('user 必须配置，且为 common\models\User 实例');
        }
    }

    /**
     * @return array
     */
    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 保存团队信息
            $this->team->save(false);
            // 保存用户信息
            $this->user->team_id = $this->team->id;
            $this->user->setAdminType();
            $this->user->setPassword($this->user->password_hash);
            $this->user->save(false);
            // 记录操作记录
            LogOperation::recordAdminOperation('创建团队:' . $this->team->name, [
                'team' => $this->team->toArray(),
                'user' => $this->user->toArray(),
            ]);
            $transaction->commit();
            return ['type' => 'success', 'msg' => '创建团队成功'];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['type' => 'error', 'msg' => '创建团队失败:' . $e->getMessage()];
        }
    }
}