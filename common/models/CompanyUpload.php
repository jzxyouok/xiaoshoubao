<?php

namespace common\models;

/**
 * This is the model class for table "company_upload".
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
 */
class CompanyUpload extends \common\models\base\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company_upload';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'registration_number', 'social_credit_code', 'organization_code', 'type_code', 'legal_person', 'establishment_date', 'registered_capital', 'business_scope', 'type_name', 'type_business', 'shareholder_information', 'leading_member', 'cellphone', 'mail', 'address', 'website', 'branch', 'business_status', 'history_name', 'province', 'business_term', 'issue_date', 'registration_authority', 'change_record'], 'string', 'max' => 255],
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
        ];
    }
}
