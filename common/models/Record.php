<?php

namespace common\models;

use frontend\components\Tools;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "record".
 *
 * @property integer $id
 * @property integer $team_id
 * @property integer $company_id
 * @property integer $type
 * @property string $content
 * @property integer $created_at
 * @property integer $created_by
 *
 * @property Company $company
 * @property Team $team
 */
class Record extends \common\models\base\ActiveRecord
{
    const TYPE_SYSTEM = 1; // 系统操作
    const TYPE_OPERATE_VISIT = 10; // 用户操作:拜访
    const TYPE_OPERATE_PHONE = 11; // 用户操作:电话联系
    const TYPE_OPERATE_QUICK = 12; // 用户操作:快速记录
    const TYPE_OPERATE_RETURN = 20; // 退回

    const CREATED_BY_SYSTEM = 0; // 操作人：系统

    public static $typeOptionData = [
        self::TYPE_OPERATE_VISIT => '拜访',
        self::TYPE_OPERATE_PHONE => '电话联系',
        self::TYPE_OPERATE_QUICK => '快速记录',
        self::TYPE_OPERATE_RETURN => '退回',
    ];

    public static $typeOptionDataUserCanSee = [
        self::TYPE_OPERATE_VISIT => '拜访',
        self::TYPE_OPERATE_PHONE => '电话联系',
        self::TYPE_OPERATE_QUICK => '快速记录',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['team_id', 'company_id', 'type'], 'required'],
            [['team_id', 'company_id', 'type', 'created_at', 'created_by'], 'integer'],
            [['content'], 'string', 'max' => 255],
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
            'type' => '类型',
            'content' => '内容',
            'created_at' => '创建时间',
            'created_by' => '创建人',
        ];
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
     * @return string
     */
    public function getTypeName()
    {
        return $this->toName($this->type, static::$typeOptionData + [static::TYPE_SYSTEM => '系统']);
    }

    /**
     * 获取格式化过的内容
     * @return mixed|string
     */
    public function getFormatContent()
    {
        $content = $this->content;
        $content = '[' . $this->getTypeName() . '] ' . $content;
        $content = str_replace('[[', '<span class="text-aqua">', $content);
        $content = str_replace(']]', '</span> ', $content);
        return $content;
    }

    /**
     * 创建系统操作记录
     * @param $teamId
     * @param $companyId
     * @param $operateUserName string 操作人
     * @param $operation string 操作
     * @param $toUserName string 操作对象
     */
    public static function createSystem($teamId, $companyId, $operateUserName, $operation, $toUserName = null)
    {
        $model = new static();
        $model->team_id = $teamId;
        $model->company_id = $companyId;
        $model->type = static::TYPE_SYSTEM;
        $model->content = '[[' . $operateUserName . ']]' . $operation . ($toUserName ? '[[' . $toUserName . ']]' : '');
        $model->created_at = time();
        $model->created_by = static::CREATED_BY_SYSTEM;
        $model->save(false);
    }

    /**
     * 创建记录
     * @param $teamId
     * @param $companyId
     * @param $type
     * @param $content
     * @throws ForbiddenHttpException
     */
    public static function createUserOperation($teamId, $companyId, $type, $content)
    {
        if (!in_array($type, array_keys(self::$typeOptionData))) {
            throw new ForbiddenHttpException('记录类型不正确');
        }
        $model = new static();
        $model->team_id = $teamId;
        $model->company_id = $companyId;
        $model->type = $type;
        $model->content = '[[' . Tools::getCurrentUser()->name . ']]:' . $content;
        $model->created_at = time();
        $model->created_by = Tools::getCurrentUserId();
        $model->save(false);
    }

}
