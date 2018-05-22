<?php

use console\migrations\Migration;

/**
 * Class m171221_113804_create_table_area
 */
class m171221_113804_create_table_area extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('province', array_merge([
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->comment('名称'),
        ], $this->commonColumns([
            'status', 'sort', 'created_at', 'created_by',
        ])
        ), $this->setTableComment('省表'));

        $this->createTable('city', array_merge([
            'id' => $this->primaryKey(),
            'province_id' => $this->integer()->notNull()->comment('省编号'),
            'name' => $this->string()->notNull()->comment('名称'),
        ], $this->commonColumns([
            'status', 'sort', 'created_at', 'created_by',
        ])
        ), $this->setTableComment('市表'));
        $this->addForeignKey('fk-city-province', 'city', 'province_id', 'province', 'id');

        $this->createTable('area', array_merge([
            'id' => $this->primaryKey(),
            'province_id' => $this->integer()->notNull()->comment('省编号'),
            'city_id' => $this->integer()->notNull()->comment('市编号'),
            'name' => $this->string()->notNull()->comment('名称'),
        ], $this->commonColumns([
            'status', 'sort', 'created_at', 'created_by',
        ])
        ), $this->setTableComment('区表'));
        $this->addForeignKey('fk-area-province', 'area', 'province_id', 'province', 'id');
        $this->addForeignKey('fk-area-city', 'area', 'city_id', 'city', 'id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('area');
        $this->dropTable('city');
        $this->dropTable('province');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171221_113804_create_table_area cannot be reverted.\n";

        return false;
    }
    */
}
