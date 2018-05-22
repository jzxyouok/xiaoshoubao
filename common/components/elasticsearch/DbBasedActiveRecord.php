<?php

namespace common\components\elasticsearch;

use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\elasticsearch\BulkCommand;

class DbBasedActiveRecord extends \yii\elasticsearch\ActiveRecord
{
    /**
     * @var string|ActiveRecord
     */
    const DB_MODEL_CLASS = '';

    /**
     * @var \yii\db\ActiveRecord
     */
    private $_dbActiveRecordObj;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!static::DB_MODEL_CLASS) {
            throw new InvalidConfigException('必须配置 DB_MODEL_CLASS');
        }
    }

    /**
     * 获取实例
     * @return ActiveRecord
     */
    protected function getDbActiveRecord()
    {
        if (!$this->_dbActiveRecordObj) {
            $class = static::DB_MODEL_CLASS;
            $this->_dbActiveRecordObj = new $class();
        }
        return $this->_dbActiveRecordObj;
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return $this->getDbActiveRecord()->attributes();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return $this->getDbActiveRecord()->rules();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return $this->getDbActiveRecord()->attributeLabels();
    }

    /**
     * 获取高亮的字段
     * 确保使用 span 方式
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-highlighting.html#specify-fragmenter
     * @param $name
     * @return mixed
     */
    public function getHighlightAttribute($name)
    {
        return isset($this->highlight[$name]) ? $this->highlight[$name][0] : $this->$name;
    }

    /**
     * @return ActiveQuery|object
     */
    public static function find()
    {
        return Yii::createObject(ActiveQuery::className(), [get_called_class()]);
    }

    /**
     * 同步数据到 ES
     * @param int $batchSize
     */
    public static function syncDataToElastic($batchSize = 1000)
    {
        $dbClass = static::DB_MODEL_CLASS;
        $bulkCommand = static::getDb()->createBulkCommand([
            'index' => static::index(),
            'type' => static::type(),
        ]);
        foreach ($dbClass::find()->batch($batchSize) as $models) {
            if (PHP_SAPI == 'cli') {
                echo date('Y-m-d H:i:s') . '- start' . PHP_EOL;
            }
            static::solveOneBatch($models, clone $bulkCommand);
            if (PHP_SAPI == 'cli') {
                echo date('Y-m-d H:i:s') . '- end' . PHP_EOL;
            }
        }
    }

    /**
     * @param $models ActiveRecord[]
     * @param $bulkCommand BulkCommand
     */
    protected static function solveOneBatch($models, $bulkCommand)
    {
        // 构建批量操作的 ES 的 body
        foreach ($models as $model) {
            $bulkCommand->addAction(
                ['index' => ['_id' => $model->primaryKey]],
                static::getSyncDataAttributes($model)
            );
        }
        $response = $bulkCommand->execute();
        // 对响应结果进行处理
        $successIds = [];
        $errorIds = [];
        foreach ($response['items'] as $item) {
            if (isset($item['index']['status']) && in_array($item['index']['status'], [200, 201])) {
                $successIds[] = $item['index']['_id'];
            } else {
                $errorIds[$item['index']['_id']] = $item['index'];
            }
        }
        Yii::info('ok:' . implode($successIds), get_called_class());
        foreach ($errorIds as $id => $error) {
            Yii::error('error:' . $id . ':' . json_encode($error, JSON_UNESCAPED_UNICODE), __CLASS__);
        }
    }

    /**
     * 获取同步数据时的字段
     * @param $model ActiveRecord
     * @return array
     */
    protected static function getSyncDataAttributes($model)
    {
        return $model->attributes;
    }

    /**
     * mapping
     * https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping.html
     * eg:
     * [
     *    static::type() => [
     *      'properties' => [
     *          'name' => ['type' => 'text'],
     *      ]
     *    ],
     * ];
     * @return array
     * @throws Exception
     */
    protected static function mapping()
    {
        throw new Exception('子类必须实现该方法');
    }

    /**
     * 额外的创建 index 时用的参数
     * @return mixed
     */
    protected static function extraIndexConfig()
    {
        return [];
    }

    /**
     * 创建 index
     */
    public static function createIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $extraConfig = static::extraIndexConfig();
        unset($extraConfig['mappings']);
        $config = array_merge([
            'settings' => [
                'index' => [
                    'number_of_shards' => 5,
                    'number_of_replicas' => 1
                ]
            ],
            'mappings' => static::mapping(),
        ], $extraConfig);
        $command->createIndex(static::index(), $config);
    }

    /**
     * 删除 index
     */
    public static function deleteIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->deleteIndex(static::index());
    }

    /**
     * 创建或更新 mapping
     */
    public static function updateMapping()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->setMapping(static::index(), static::type(), static::mapping());
    }
}