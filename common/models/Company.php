<?php

namespace common\models;

use yii\base\Exception;

/**
 * This is the model class for table "company".
 *
 * @property integer $id
 * @property string $name
 * @property string $registration_number
 * @property string $social_credit_code
 * @property string $organization_code
 * @property string $type_code
 * @property string $legal_person
 * @property string $establishment_date
 * @property string $registered_capital
 * @property string $business_scope
 * @property string $type_name
 * @property string $type_business
 * @property string $shareholder_information
 * @property string $leading_member
 * @property string $cellphone
 * @property string $mail
 * @property string $address
 * @property string $website
 * @property string $branch
 * @property string $business_status
 * @property string $history_name
 * @property string $province
 * @property string $business_term
 * @property string $issue_date
 * @property string $registration_authority
 * @property string $change_record
 * @property integer $status
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 *
 * @property Clue[] $clues
 * @property Record[] $records
 * @property UserClue[] $userClues
 */
class Company extends \common\models\base\ActiveRecord
{
    const STATUS_NORMAL = 0;
    const STATUS_DISABLE = 10;

    const REGISTERED_CAPITAL_UNIT = '万元人民币'; // 注册资本单位

    public static $statusData = [
        self::STATUS_NORMAL => '正常',
        self::STATUS_DISABLE => '禁用',
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
        return 'company';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [[
                'name', 'registration_number', 'social_credit_code', 'organization_code', 'type_code', 'legal_person',
                'establishment_date', 'business_scope', 'type_name', 'type_business',
                'shareholder_information', 'leading_member', 'cellphone', 'mail', 'address', 'website', 'branch',
                'business_status', 'history_name', 'province', 'business_term', 'issue_date', 'registration_authority',
                'change_record'
            ], 'string', 'max' => 255],
            [['registered_capital'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'registration_number' => '注册号',
            'social_credit_code' => '社会信用代码',
            'organization_code' => '组织机构代码',
            'type_code' => '企业分类',
            'legal_person' => '公司法人',
            'establishment_date' => '成立日期',
            'registered_capital' => '注册资本',
            'business_scope' => '一般经营范围',
            'type_name' => '企业类型',
            'type_business' => '类型',
            'shareholder_information' => '股东信息',
            'leading_member' => '主要成员',
            'cellphone' => '电话',
            'mail' => '邮箱',
            'address' => '地址',
            'website' => '网址',
            'branch' => '分支机构',
            'business_status' => '状态',
            'history_name' => '历史名称',
            'province' => '省份',
            'business_term' => '营业期限',
            'issue_date' => '发照日期',
            'registration_authority' => '登记机关',
            'change_record' => '变更记录',
            'status' => '状态',
            'created_at' => '创建时间',
            'created_by' => '创建人',
            'updated_at' => '修改时间',
            'updated_by' => '修改人',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        CompanyMigration::createOne($this->id, $insert ? CompanyMigration::OPERATION_INSERT : CompanyMigration::OPERATION_UPDATE);
        parent::afterSave($insert, $changedAttributes);
    }

    public function delete()
    {
        // 删除之前记录当前的编号，用于删除 ES 的数据
        $companyId = $this->id;
        try {
            $result = parent::delete();
        } catch (Exception $e) {
            $this->addError('id', '该企业信息已经被其他企业领取，不能删除');
            return false;
        }
        if ($result) {
            CompanyMigration::createOne($companyId, CompanyMigration::OPERATION_DELETE);
        }
        return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClues()
    {
        return $this->hasMany(Clue::className(), ['company_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecords()
    {
        return $this->hasMany(Record::className(), ['company_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserClues()
    {
        return $this->hasMany(UserClue::className(), ['company_id' => 'id']);
    }

}
