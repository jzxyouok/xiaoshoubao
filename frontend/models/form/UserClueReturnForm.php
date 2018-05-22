<?php

namespace frontend\models\form;

use common\models\Clue;
use common\models\Record;
use common\models\UserClue;
use frontend\components\Tools;
use Yii;
use yii\base\Exception;
use yii\base\Model;

class UserClueReturnForm extends Model
{
    public $user_id;

    public $company_id;

    public $remark;

    public function rules()
    {
        return [
            [['user_id', 'company_id', 'remark'], 'required'],
            [['user_id', 'company_id'], 'integer'],
            ['remark', 'string', 'max' => 200]
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => '用户编号',
            'company_id' => '公司编号',
            'remark' => '退回理由',
        ];
    }

    public function return ()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 删除用户线索
            UserClue::deleteAll(['user_id' => $this->user_id, 'company_id' => $this->company_id]);
            // 公海线索状态恢复
            $clue = Clue::findInTeam()->andWhere(['company_id' => $this->company_id, 'status' => Clue::STATUS_USER_PICKED])->one();
            $clue->status = Clue::STATUS_NORMAL;
            $clue->save(false);
            // 记录信息
            Record::createSystem(Tools::getCurrentUserTeamId(), $this->company_id, Tools::getCurrentUser()->name, '退回线索');
            Record::createUserOperation(Tools::getCurrentUserTeamId(), $this->company_id, Record::TYPE_OPERATE_RETURN, '退回理由:' . $this->remark);
            $transaction->commit();
            return ['type' => 'success', 'msg' => '线索退回成功'];
        } catch (Exception $exception) {
            $transaction->rollBack();
            return ['type' => 'error', 'msg' => $exception->getMessage()];
        }
    }
}