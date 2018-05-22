<?php

use yii\db\Migration;

class m171110_052714_auth extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // 后台权限
        $this->createTable('{{%admin_auth_operation}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->notNull()->defaultValue(0)->comment('父权限'),
            'name' => $this->string()->notNull()->comment('权限操作名称'),
        ], $tableOptions . ' COMMENT=\'后台权限操作表\'');
        $this->createIndex('parent_id', '{{%admin_auth_operation}}', 'parent_id');

        $this->createTable('{{%admin_auth_role}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->comment('角色名称'),
            'description' => $this->string()->comment('描述'),
            'operation_list' => $this->text()->comment('权限列表'),
        ], $tableOptions . ' COMMENT=\'后台权限角色表\'');

        $this->addColumn('{{%admin}}', 'auth_role', $this->string()->comment('权限角色'));

        // 前台权限
        $this->createTable('{{%user_auth_operation}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->notNull()->defaultValue(0)->comment('父权限'),
            'name' => $this->string()->notNull()->comment('权限操作名称'),
        ], $tableOptions . ' COMMENT=\'前台权限操作表\'');
        $this->createIndex('parent_id', '{{%user_auth_operation}}', 'parent_id');

        $this->createTable('{{%user_auth_role}}', [
            'id' => $this->primaryKey(),
            'team_id' => $this->integer()->notNull()->comment('团队'),
            'name' => $this->string()->notNull()->comment('角色名称'),
            'description' => $this->string()->comment('描述'),
            'operation_list' => $this->text()->comment('权限列表'),
        ], $tableOptions . ' COMMENT=\'前台权限角色表\'');

        $this->addColumn('{{%user}}', 'auth_role', $this->string()->comment('权限角色'));
    }

    public function down()
    {
        echo "m170310_052714_auth cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
