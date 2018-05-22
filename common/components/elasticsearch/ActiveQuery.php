<?php

namespace common\components\elasticsearch;

class ActiveQuery extends \yii\elasticsearch\ActiveQuery
{
    /**
     * 修改注释参数可以为 数组
     * @inheritdoc
     * @param string|array $query
     * @return \yii\elasticsearch\ActiveQuery
     */
    public function query($query)
    {
        return parent::query($query);
    }

    /**
     * 快速处理高亮字段
     * @param $fields array eg:['name', 'field']
     * @return \yii\elasticsearch\ActiveQuery
     */
    public function highlightQuick($fields)
    {
        $highlightFields = [];
        foreach ($fields as $field) {
            $highlightFields[$field] = ['type' => 'plain'];
        }
        return parent::highlight(['fields' => $highlightFields]);
    }
}