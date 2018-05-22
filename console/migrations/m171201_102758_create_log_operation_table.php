<?php

use console\migrations\Migration;

/**
 * Class m171201_102758_add_ip
 */
class m171201_102758_create_log_operation_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('log_operation', array_merge([
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->comment('用户编号'),
            'user_type' => $this->integer()->notNull()->comment('用户类型'),
            'operation' => $this->string()->notNull()->comment('操作'),
            'remark' => $this->text()->comment('备注'),
            'ip' => $this->string()->notNull()->comment('ip'),
        ], $this->commonColumns([
            'created_at',
        ])
        ), $this->setTableComment('操作日志表'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('log_operation');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171201_102758_add_ip cannot be reverted.\n";

        return false;
    }
    */
}
