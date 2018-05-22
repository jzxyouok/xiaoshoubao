<?php

namespace common\models;

use common\models\elasticsearch\Company;
use frontend\components\Tools;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "user_clue".
 *
 * @property integer $id
 * @property integer $team_id
 * @property integer $user_id
 * @property integer $company_id
 * @property integer $status
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 *
 * @property User $user
 * @property Company $company
 * @property Team $team
 */
class UserClue extends \common\models\base\ActiveRecord
{
    const STATUS_NORMAL = 0; // 正常

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
        return 'user_clue';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['team_id', 'company_id'], 'required'],
            [['team_id', 'user_id', 'company_id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
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
            'user_id' => '用户',
            'company_id' => '企业',
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
            $current = UserClue::find()->where(['team_id' => $this->team_id, 'user_id' => $this->user_id, 'status' => static::STATUS_NORMAL])->count('id');
            $max = User::find()->select(['max_clue_size'])->where(['team_id' => $this->team_id, 'id' => $this->user_id])->scalar();
            if ($current >= (int)$max) {
                $this->addError('user_id', '已达到用户私海保有量最大值:' . $max);
                return false;
            }
        }

        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Team::className(), ['id' => 'team_id']);
    }

    /**
     * 用户是否已经领取该线索
     * 只能前台用户登录后调用该方法
     * @param $companyId
     * @return bool
     * @throws ForbiddenHttpException
     */
    public static function isUserPicked($companyId)
    {
        $has = static::find()->select(['id'])->where(['user_id' => Tools::getCurrentUserId(), 'company_id' => $companyId])->one();
        return (bool)$has;
    }

}
