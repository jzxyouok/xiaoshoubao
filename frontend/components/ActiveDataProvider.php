<?php

namespace frontend\components;

class ActiveDataProvider extends \common\components\ActiveDataProvider
{
    /**
     * @inheritdoc
     */
    public function setPagination($value)
    {
        if (is_array($value)) {
            $value = array_merge([
                'defaultPageSize' => 10
            ], $value);
        }
        parent::setPagination($value);
    }
}