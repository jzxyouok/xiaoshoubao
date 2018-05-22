<?php

namespace common\models;

use yii\base\Exception;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "team".
 *
 * @property integer $id
 * @property string $name
 * @property integer $past_at
 * @property integer $max_clue_size
 * @property integer $current_clue_size
 * @property integer $status
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 *
 * @property Clue[] $clues
 * @property Department[] $departments
 * @property Record[] $records
 * @property User[] $users
 * @property UserClue[] $userClues
 * @property string $statusName
 */
class Team extends \common\models\base\ActiveRecord
{
    const STATUS_NORMAL = 0;
    const STATUS_DISABLE = 10;

    public static $statusData = [
        self::STATUS_NORMAL => '正常',
        self::STATUS_DISABLE => '不可用',
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
        return 'team';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'past_at', 'max_clue_size'], 'required'],
            [['past_at', 'max_clue_size', 'current_clue_size', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '团队名称',
            'past_at' => '过期时间',
            'max_clue_size' => '最大领取线索量',
            'current_clue_size' => '当前领取线索量',
            'status' => '状态',
            'created_at' => '创建时间',
            'created_by' => '创建人',
            'updated_at' => '修改时间',
            'updated_by' => '修改人',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClues()
    {
        return $this->hasMany(Clue::className(), ['team_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartments()
    {
        return $this->hasMany(Department::className(), ['team_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecords()
    {
        return $this->hasMany(Record::className(), ['team_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['team_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserClues()
    {
        return $this->hasMany(UserClue::className(), ['team_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        return $this->toName($this->status, self::$statusData);
    }

    /**
     * 增加当前线索量
     * @param $id
     * @param int $increaseNumber
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public static function increaseCurrentClueSize($id, $increaseNumber = 1)
    {
        $model = Team::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('未找到团队');
        }
        if ($model->current_clue_size >= $model->max_clue_size) {
            throw new Exception('已经超过最大领取线索量:' . $model->max_clue_size);
        }
        $model->current_clue_size += $increaseNumber;
        $model->save(false);
    }
}
