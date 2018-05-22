<?php

namespace common\models;

use common\components\Tools;
use common\models\base\ActiveRecord;
use kriss\components\CellphoneValidator;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property integer $team_id
 * @property integer $department_id
 * @property integer $type
 * @property string $cellphone
 * @property string $password_hash
 * @property string $name
 * @property string $auth_key
 * @property integer $max_clue_size
 * @property integer $status
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 * @property string $auth_role
 *
 * @property Clue[] $clues
 * @property Record[] $records
 * @property Team $team
 * @property null|Department $department
 * @property UserClue[] $userClues
 * @property string $typeName
 * @property string $statusName
 */
class User extends ActiveRecord implements IdentityInterface
{
    const TYPE_ADMIN = 10; // 团队管理员
    const TYPE_USER = 20; // 团队成员

    const STATUS_NORMAL = 0; // 正常
    const STATUS_DISABLE = 10; // 不可登录

    public static $typeData = [
        self::TYPE_ADMIN => '管理员',
        self::TYPE_USER => '普通账户',
    ];

    public static $statusData = [
        self::STATUS_NORMAL => '正常',
        self::STATUS_DISABLE => '不可用',
    ];

    /**
     * @var bool
     */
    private $_hasSetUserType = false;

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
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cellphone', 'password_hash', 'name', 'max_clue_size'], 'required'],
            [['team_id', 'department_id', 'type', 'max_clue_size', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['cellphone'], 'string', 'max' => 11],
            [['password_hash', 'name', 'auth_key'], 'string', 'max' => 255],
            [['cellphone'], 'unique'],
            [['cellphone'], CellphoneValidator::className()],
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
            'team_id' => '团队编号',
            'department_id' => '部门编号',
            'type' => '用户类型',
            'cellphone' => '手机号',
            'password_hash' => '密码',
            'name' => '姓名',
            'auth_key' => 'Auth Key',
            'max_clue_size' => '私海保有量',
            'status' => '状态',
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

        if ($this->isNewRecord && !$this->_hasSetUserType) {
            throw new Exception('必须先调用 setAdminType 或 setNormalType');
        }

        if (!$this->department_id) {
            $this->department_id = 0;
        }

        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClues()
    {
        return $this->hasMany(Clue::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecords()
    {
        return $this->hasMany(Record::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Team::className(), ['id' => 'team_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Department::className(), ['id' => 'department_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserClues()
    {
        return $this->hasMany(UserClue::className(), ['created_by' => 'id']);
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return $this->toName($this->type, self::$typeData);
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        return $this->toName($this->status, self::$statusData);
    }

    /**
     * @inheritDoc
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    /**
     * @inheritDoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException();
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->primaryKey;
    }

    /**
     * @inheritDoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritDoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * 生成auth_key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Tools::generateRandString();
    }

    /**
     * 设置密码
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Tools::generatePasswordHash($password);
    }

    /**
     * 校验密码
     * @param $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return Tools::validatePassword($password, $this->password_hash);
    }

    /**
     * 设置为admin类型
     */
    public function setAdminType()
    {
        $this->type = self::TYPE_ADMIN;
        if (!$this->team_id) {
            throw new Exception('不存在 team_id');
        }
        $userAuthRole = UserAuthRole::getSuperAdminRole($this->team_id);
        $this->auth_role = $userAuthRole->id;
        $this->generateAuthKey();
        $this->_hasSetUserType = true;
    }

    /**
     * 设置为普通用户类型
     */
    public function setNormalType()
    {
        $this->type = self::TYPE_USER;
        $this->generateAuthKey();
        $this->_hasSetUserType = true;
    }
}
