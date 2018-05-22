<?php

use console\migrations\Migration;

/**
 * Handles the creation of table `job_schedule`.
 */
class m171208_064921_create_job_schedule_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('job_schedule', array_merge([
            'id' => $this->primaryKey(),
            'job_name' => $this->string()->notNull()->comment('任务名称'),
            'schedule_percent' => $this->decimal(5, 2)->defaultValue(0)->notNull()->comment('进度'),
            'current_description' => $this->string()->comment('当前进度描述'),
        ], $this->commonColumns([
            'status', 'created_at', 'created_by', 'updated_at'
        ])
        ), $this->setTableComment('任务进度表'));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('job_schedule');
    }
}
