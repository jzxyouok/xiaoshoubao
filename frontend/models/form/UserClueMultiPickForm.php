<?php

namespace frontend\models\form;

use common\models\Clue;
use common\models\Record;
use common\models\User;
use common\models\UserClue;
use frontend\components\Tools;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\web\ForbiddenHttpException;

class UserClueMultiPickForm extends Model
{
    const TYPE_PICK = 'pick';
    const TYPE_DISTRIBUTE = 'distribute';

    public $type;

    public $user_id;

    public $company_ids;

    public function rules()
    {
        return [
            [['user_id', 'company_id'], 'required'],
            [['user_id'], 'integer'],
            [['company_ids'], 'each', 'rule' => ['integer']]
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => '用户编号',
            'company_id' => '公司编号'
        ];
    }

    public function init()
    {
        if (!$this->type) {
            throw new InvalidConfigException('type 必须配置');
        }
    }

    /**
     * 创建个人线索
     * @return bool
     */
    public function create()
    {
        $clue = Clue::findInTeam()->andWhere(['company_id' => $this->company_id])->one();
        if (!$clue) {
            $this->addError('company_id', '未找到该线索');
            return false;
        }
        if ($clue->status == Clue::STATUS_USER_PICKED) {
            $this->addError('company_id', '该线索已经被领取，请勿重复操作');
            return false;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 修改公海池线索状态
            $clue->status = Clue::STATUS_USER_PICKED;
            $clue->save(false);
            // 增加或修改个人线索
            $model = new UserClue();
            $model->team_id = Tools::getCurrentUserTeamId();
            $model->user_id = $this->user_id;
            $model->company_id = $this->company_id;
            $model->save(false);
            // 记录变化信息
            if ($this->type == self::TYPE_PICK) {
                Record::createSystem(Tools::getCurrentUserTeamId(), $this->company_id, Tools::getCurrentUser()->name, '领取线索到私海');
            } elseif ($this->type == self::TYPE_DISTRIBUTE) {
                $userName = User::find()->select(['name'])->where(['id' => $this->user_id])->scalar();
                Record::createSystem(Tools::getCurrentUserTeamId(), $this->company_id, Tools::getCurrentUser()->name, '分配线索给', $userName);
            } else {
                throw new ForbiddenHttpException('未知的类型');
            }
            $transaction->commit();
        } catch (Exception $exception) {
            $transaction->rollBack();
            $this->addError('company_id', $exception->getMessage());
            return false;
        }
        return true;
    }
}