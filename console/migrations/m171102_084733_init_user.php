<?php

class m171102_084733_init_user extends \console\migrations\Migration
{
    public function safeUp()
    {
        $this->createTable('admin', array_merge([
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique()->comment('登录名'),
            'password_hash' => $this->string()->notNull()->comment('密码'),
            'name' => $this->string()->notNull()->comment('管理员姓名'),
            'auth_key' => $this->string()->notNull()->comment('Auth Key'),
        ], $this->commonColumns([
            'status', 'created_at', 'created_by', 'updated_at', 'updated_by'
        ])
        ), $this->setTableComment('管理员表'));

        $this->createTable('team', array_merge([
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->comment('团队名称'),
            'past_at' => $this->integer()->notNull()->comment('过期时间'),
            'max_clue_size' => $this->integer()->notNull()->comment('最大领取线索量'),
            'current_clue_size' => $this->integer()->notNull()->defaultValue(0)->comment('当前领取线索量'),
        ], $this->commonColumns([
            'status', 'created_at', 'created_by', 'updated_at', 'updated_by'
        ])
        ), $this->setTableComment('团队表'));

        $this->createTable('user', array_merge([
            'id' => $this->primaryKey(),
            'team_id' => $this->integer()->null()->comment('团队编号'),
            'department_id' => $this->integer()->notNull()->defaultValue(0)->comment('部门编号'),
            'type' => $this->integer()->notNull()->comment('用户类型:10团队管理员，20团队成员'),
            'cellphone' => $this->string(11)->notNull()->unique()->comment('手机号'),
            'password_hash' => $this->string()->notNull()->comment('密码'),
            'name' => $this->string()->notNull()->comment('姓名'),
            'auth_key' => $this->string()->notNull()->comment('Auth Key'),
            'max_clue_size' => $this->integer()->notNull()->comment('私海保有量')
        ], $this->commonColumns([
            'status', 'created_at', 'created_by', 'updated_at', 'updated_by'
        ])
        ), $this->setTableComment('团队用户表'));
        $this->addForeignKey('fx-user-team_id', 'user', 'team_id', 'team', 'id');

        $this->createTable('department', array_merge([
            'id' => $this->primaryKey(),
            'team_id' => $this->integer()->null()->comment('团队编号'),
            'name' => $this->string()->notNull()->comment('部门名称'),
        ], $this->commonColumns([
            'status', 'created_at', 'created_by', 'updated_at', 'updated_by'
        ])
        ), $this->setTableComment('部门表'));
        $this->addForeignKey('fx-department-team_id', 'department', 'team_id', 'team', 'id');

        $companyAttributes = [
            'name' => $this->string()->comment('名称'),
            'registration_number' => $this->string()->comment('注册号'),
            'social_credit_code' => $this->string()->comment('社会信用代码'),
            'organization_code' => $this->string()->comment('组织机构代码'),
            'type_code' => $this->string()->comment('企业分类'),
            'legal_person' => $this->string()->comment('公司法人'),
            'establishment_date' => $this->string()->comment('成立日期'),
            'registered_capital' => $this->string()->comment('注册资本'),
            'business_scope' => $this->string()->comment('一般经营范围'),
            'type_name' => $this->string()->comment('企业类型'),
            'type_business' => $this->string()->comment('类型'),
            'shareholder_information' => $this->string()->comment('股东信息'),
            'leading_member' => $this->string()->comment('主要成员'),
            'cellphone' => $this->string()->comment('电话'),
            'mail' => $this->string()->comment('邮箱'),
            'address' => $this->string()->comment('地址'),
            'website' => $this->string()->comment('网址'),
            'branch' => $this->string()->comment('分支机构'),
            'business_status' => $this->string()->comment('状态'),
            'history_name' => $this->string()->comment('历史名称'),
            'province' => $this->string()->comment('省份'),
            'business_term' => $this->string()->comment('营业期限'),
            'issue_date' => $this->string()->comment('发照日期'),
            'registration_authority' => $this->string()->comment('登记机关'),
            'change_record' => $this->string()->comment('变更记录'),
        ];

        $this->createTable('company_upload', array_merge([
            'id' => $this->primaryKey(),
        ], $companyAttributes), $this->setTableComment('企业信息-上传表'));

        $this->createTable('company', array_merge(
            array_merge(['id' => $this->primaryKey()], $companyAttributes),
            $this->commonColumns([
                'status', 'created_at', 'created_by', 'updated_at', 'updated_by'
            ])
        ), $this->setTableComment('企业信息表'));

        $this->createTable('clue', array_merge([
            'id' => $this->primaryKey(),
            'team_id' => $this->integer()->notNull()->comment('团队'),
            'company_id' => $this->integer()->notNull()->comment('企业'),
            'return_number' => $this->integer()->notNull()->defaultValue(0)->comment('被退回次数')
        ], $this->commonColumns([
            'status', 'created_at', 'created_by', 'updated_at', 'updated_by'
        ])
        ), $this->setTableComment('线索表'));
        $this->addForeignKey('fx-clue-team_id', 'clue', 'team_id', 'team', 'id');
        $this->addForeignKey('fx-clue-company_id', 'clue', 'company_id', 'company', 'id');

        $this->createTable('user_clue', array_merge([
            'id' => $this->primaryKey(),
            'team_id' => $this->integer()->notNull()->comment('团队'),
            'user_id' => $this->integer()->notNull()->comment('用户'),
            'company_id' => $this->integer()->notNull()->comment('企业'),
        ], $this->commonColumns([
            'status', 'created_at', 'created_by', 'updated_at', 'updated_by'
        ])
        ), $this->setTableComment('用户线索表'));
        $this->addForeignKey('fx-user_clue-team_id', 'user_clue', 'team_id', 'team', 'id');
        $this->addForeignKey('fx-user_clue-user_id', 'user_clue', 'user_id', 'user', 'id');
        $this->addForeignKey('fx-user_clue-company_id', 'user_clue', 'company_id', 'company', 'id');

        $this->createTable('record', array_merge([
            'id' => $this->primaryKey(),
            'team_id' => $this->integer()->notNull()->comment('团队'),
            'company_id' => $this->integer()->notNull()->comment('企业'),
            'type' => $this->integer()->notNull()->comment('类型'),
            'content' => $this->string()->comment('内容'),
        ], $this->commonColumns([
            'created_at', 'created_by',
        ])
        ), $this->setTableComment('线索记录'));
        $this->addForeignKey('fx-record-team_id', 'record', 'team_id', 'team', 'id');
        $this->addForeignKey('fx-record-company_id', 'record', 'company_id', 'company', 'id');

        $this->createTable('clue_pick_record', array_merge([
            'id' => $this->primaryKey(),
            'team_id' => $this->integer()->notNull()->comment('团队编号'),
            'company_id' => $this->integer()->notNull()->comment('企业编号'),
        ], $this->commonColumns([
            'created_at', 'created_by'
        ])
        ), $this->setTableComment('线索领取记录表'));
    }

    public function safeDown()
    {
        echo "m171102_084733_init_user cannot be reverted.\n";

        return false;
    }
}
