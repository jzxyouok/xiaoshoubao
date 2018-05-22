<?php

namespace frontend\models\form;

use common\models\Clue;
use common\models\Record;
use frontend\components\Tools;
use Yii;
use yii\base\Exception;
use yii\base\Model;

class CluePickForm extends Model
{
    public $company_id;

    public function rules()
    {
        return [
            [['company_id'], 'required'],
            [['company_id'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'company_id' => '公司编号'
        ];
    }

    /**
     * 创建线索
     * @return array
     */
    public function create()
    {
        $has = Clue::isTeamPicked($this->company_id);
        if ($has) {
            return ['type' => 'error', 'msg' => '已经领取过该线索，请勿重复领取'];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 记录团队线索
            $model = new Clue();
            $model->team_id = Tools::getCurrentUserTeamId();
            $model->company_id = $this->company_id;
            $isSuccess = $model->save(false);
            if (!$isSuccess) {
                return ['type' => 'error', 'msg' => '线索领取失败:' . Tools::getFirstError($model->errors)];
            }
            // 记录操作记录
            Record::createSystem(Tools::getCurrentUserTeamId(), $this->company_id, Tools::getCurrentUser()->name, '领取线索到公海');
            $transaction->commit();
            return ['type' => 'success', 'msg' => '线索领取成功'];
        } catch (Exception $exception) {
            $transaction->rollBack();
            return ['type' => 'error', 'msg' => $exception->getMessage()];
        }
    }
}