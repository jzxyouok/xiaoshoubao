<?php

use console\migrations\Migration;

/**
 * Handles the creation of table `company_migration`.
 */
class m171207_014437_create_company_migration_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('company_migration', array_merge([
            'id' => $this->primaryKey(),
            'company_id' => $this->integer()->notNull()->comment('企业编号'),
            'operation' => $this->string()->notNull()->comment('操作'),
        ], $this->commonColumns([
            'status', 'created_at', 'updated_at'
        ])
        ), $this->setTableComment('企业信息迁移表'));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('company_migration');
    }
}
