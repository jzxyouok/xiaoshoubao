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

class UserCluePickForm extends Model
{
    const TYPE_PICK = 'pick';
    const TYPE_DISTRIBUTE = 'distribute';

    /**
     * 操作方式
     * @var string
     */
    public $type;
    /**
     * 用户编号
     * @var integer
     */
    public $user_id;
    /**
     * 企业编号
     * 创建单个线索时必须
     * @var integer
     */
    public $company_id;
    /**
     * 企业编号
     * 创建多个线索时必须
     * @var string
     */
    public $company_ids;

    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'company_id'], 'integer'],
            [['company_ids'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => '用户编号',
            'company_id' => '公司编号',
            'company_ids' => '公司编号集合',
        ];
    }

    public function init()
    {
        if (!$this->type) {
            throw new InvalidConfigException('type 必须配置');
        }
    }

    /**
     * @throws InvalidConfigException
     */
    public function create()
    {
        if ($this->company_id) {
            return $this->createOne($this->company_id);
        } elseif ($this->company_ids) {
            $companyIdsArr = array_filter(explode(',', $this->company_ids));
            return $this->createMulti($companyIdsArr);
        } else {
            throw new InvalidConfigException('必须配置 company_id 或 company_ids');
        }
    }

    /**
     * 创建单个
     * @param $companyId
     * @return array
     */
    protected function createOne($companyId)
    {
        $clue = Clue::findInTeam()->andWhere(['company_id' => $companyId])->one();
        if (!$clue) {
            return ['type' => 'error', 'msg' => '未找到该线索'];
        }
        if ($clue->status == Clue::STATUS_USER_PICKED) {
            return ['type' => 'warning', 'msg' => '该线索已经被领取，请勿重复操作'];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($this->type == self::TYPE_PICK) {
                $pickOrDistributeMsg = '领取线索';
            } elseif ($this->type == self::TYPE_DISTRIBUTE) {
                $pickOrDistributeMsg = '分配线索';
            } else {
                throw new InvalidConfigException('type 未知');
            }
            // 修改公海池线索状态
            $clue->status = Clue::STATUS_USER_PICKED;
            $clue->save(false);
            // 增加或修改个人线索
            $model = new UserClue();
            $model->team_id = Tools::getCurrentUserTeamId();
            $model->user_id = $this->user_id;
            $model->company_id = $companyId;
            $isSuccess = $model->save(false);
            if (!$isSuccess) {
                return ['type' => 'error', 'msg' => $pickOrDistributeMsg . '失败:' . Tools::getFirstError($model->errors)];
            }
            // 记录变化信息
            if ($this->type == self::TYPE_PICK) {
                Record::createSystem(Tools::getCurrentUserTeamId(), $companyId, Tools::getCurrentUser()->name, '领取线索到私海');
            } elseif ($this->type == self::TYPE_DISTRIBUTE) {
                $userName = User::find()->select(['name'])->where(['id' => $this->user_id])->scalar();
                Record::createSystem(Tools::getCurrentUserTeamId(), $companyId, Tools::getCurrentUser()->name, '分配线索给', $userName);
            } else {
                throw new InvalidConfigException('type 未知');
            }
            $transaction->commit();
            return ['type' => 'success', 'msg' => $pickOrDistributeMsg . '成功'];
        } catch (Exception $exception) {
            $transaction->rollBack();
            return ['type' => 'error', 'msg' => $exception->getMessage()];
        }
    }

    /**
     * 创建多个
     * @param $companyIds
     * @return array
     */
    protected function createMulti($companyIds)
    {
        $successCount = 0;
        foreach ($companyIds as $companyId) {
            $result = $this->createOne($companyId);
            if ($result['type'] == 'success') {
                $successCount++;
            }
        }
        return ['type' => 'success', 'msg' => '操作成功，成功分配' . $successCount . '条线索'];
    }
}