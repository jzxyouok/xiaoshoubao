<?php

namespace common\components;

use Yii;

class ActiveDataProvider extends \yii\data\ActiveDataProvider
{
    /**
     * 是否允许统计数量缓存
     * @var bool
     */
    public $enableCountCache = false;
    /**
     * 缓存时间
     * @var int
     */
    public $countCacheTime = 60;

    /**
     * @inheritdoc
     */
    public function getTotalCount()
    {
        if (!$this->enableCountCache) {
            return parent::getTotalCount();
        }
        // 缓存数量统计（针对不太变化的数据）
        return Yii::$app->cache->getOrSet([__CLASS__, __FUNCTION__, $this->query], function () {
            return parent::getTotalCount();
        }, $this->countCacheTime);
    }
}