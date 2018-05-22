<?php

namespace common\components\elasticsearch;

class ActiveDataProvider extends \yii\elasticsearch\ActiveDataProvider
{
    public function setPagination($value)
    {
        if (is_array($value)) {
            $value = array_merge([
                'defaultPageSize' => 10,
            ], $value);
        }
        parent::setPagination($value);
    }

    /*public function prepareTotalCount()
    {
        // 最多查询显示 10000 条数据，超过需要改 elasticSearch 的配置，不然会报错
        $count = parent::prepareTotalCount();
        if ($count > 10000) {
            return 10000;
        }
        return $count;
    }*/
}