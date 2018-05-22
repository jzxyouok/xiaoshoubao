<?php

namespace common\models;

use common\models\elasticsearch\Company;
use yii\base\Exception;

/**
 * This is the model class for table "clue".
 *
 * @property integer $id
 * @property integer $team_id
 * @property integer $company_id
 * @property integer $return_number
 * @property integer $status
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 *
 * @property Company $company
 * @property Team $team
 * @property string $statusName
 */
class Clue extends \common\models\base\ActiveRecord
{
    const STATUS_NORMAL = 0; // 团队已领取，等待分配或等待被个人领取
    const STATUS_USER_PICKED = 1; // 个人已领取

    public static $statusData = [
        self::STATUS_NORMAL => '待分配',
        self::STATUS_USER_PICKED => '已领取',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['time_user'] = [
            'class' => 'kriss\components\TimeUserBehavior',
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'clue';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['team_id', 'company_id'], 'required'],
            [['team_id', 'company_id', 'return_number', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['team_id'], 'exist', 'skipOnError' => true, 'targetClass' => Team::className(), 'targetAttribute' => ['team_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'team_id' => '团队',
            'company_id' => '企业',
            'return_number' => '被退回次数',
            'status' => '状态',
            'created_at' => '创建时间',
            'created_by' => '创建人',
            'updated_at' => '修改时间',
            'updated_by' => '修改人',
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->isNewRecord) {
            try {
                Team::increaseCurrentClueSize($this->team_id);
                CluePickRecord::createOne($this->team_id, $this->company_id);
            } catch (Exception $e) {
                $this->addError('team_id', $e->getMessage());
                return false;
            }
        }

        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        return $this->toName($this->status, static::$statusData);
    }

    /**
     * 企业是否已经领取该线索
     * 只能前台用户登录后调用该方法
     * @param $companyId
     * @return bool
     */
    public static function isTeamPicked($companyId)
    {
        $has = static::findInTeam()->select(['id'])->andWhere(['company_id' => $companyId])->one();
        return (bool)$has;
    }

    /**
     * 是否已被个人领取
     * @param $companyId
     * @return bool
     */
    public static function isUserPicked($companyId)
    {
        $has = static::find()->select(['id'])->where(['status' => static::STATUS_USER_PICKED, 'company_id' => $companyId])->one();
        return (bool)$has;
    }

}
