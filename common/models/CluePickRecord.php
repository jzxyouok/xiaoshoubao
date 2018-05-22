<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "clue_pick_record".
 *
 * @property integer $id
 * @property integer $team_id
 * @property integer $company_id
 * @property integer $created_at
 * @property integer $created_by
 */
class CluePickRecord extends \common\models\base\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'clue_pick_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['team_id', 'company_id'], 'required'],
            [['team_id', 'company_id', 'created_at', 'created_by'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'team_id' => '团队编号',
            'company_id' => '企业编号',
            'created_at' => '创建时间',
            'created_by' => '创建人',
        ];
    }

    /**
     * 创建一条记录
     * @param $teamId
     * @param $companyId
     */
    public static function createOne($teamId, $companyId)
    {
        $model = new static();
        $model->team_id = $teamId;
        $model->company_id = $companyId;
        $model->created_at = time();
        if (PHP_SAPI == 'cli' || Yii::$app->user->isGuest) {
            $createdBy = 0;
        } else {
            $createdBy = Yii::$app->user->id;
        }
        $model->created_by = $createdBy;
        $model->save(false);
    }
}
