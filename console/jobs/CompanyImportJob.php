<?php

namespace console\jobs;

use common\models\CompanyUpload;
use console\jobs\base\BaseJobSchedule;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\BaseReader;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * 提交 Excel 文件，导入到 CompanyUpload 表
 */
class CompanyImportJob extends BaseJobSchedule
{
    /**
     * 导入文件
     * @var string
     */
    public $filename;
    /**
     * 导入数据库表名
     * @var string
     */
    public $tableName;

    /**
     * @inheritdoc
     */
    public function jobExecute($queue)
    {
        $jobSchedule = $this->getJobSchedule();
        $jobSchedule->updatePercent(1, 2, '加载文件数据');
        $allData = $this->loadData();
        $jobSchedule->updatePercent(2, 2, '加载文件数据');

        $splitData = array_chunk($allData, 500, true);
        $totalBulk = count($splitData);
        foreach ($splitData as $key => $data) {
            $result = Yii::$app->db->createCommand()->batchInsert(
                $this->tableName,
                array_keys(static::getImportAttributesMaps()),
                $data
            )->execute();

            $jobSchedule->updatePercent($key, $totalBulk, '导入数据库');
            $this->logger(implode(':', ['dump2database', 'total', count($data), 'success', $result]), 'trace');
        }
    }

    /**
     * 读取文件中的数据
     * @return array
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    protected function loadData()
    {
        /** @var BaseReader $reader */
        $reader = IOFactory::createReaderForFile($this->filename);
        // 只读取数据
        $reader->setReadDataOnly(true);
        // 只读取第一张 sheet 页
        $reader->setLoadSheetsOnly(0);
        $spreadsheet = $reader->load($this->filename);
        $activeSheet = $spreadsheet->getActiveSheet();
        // 检查列数和预定的是否一致，防止之后的 array_combine 出错
        $maxColIndex = Coordinate::columnIndexFromString($activeSheet->getHighestColumn());
        if ($maxColIndex != count(static::getImportAttributesMaps())) {
            $msg = '导入的表格列数和预定的不一致,文件名:' . basename($this->filename);
            $this->logger($msg, 'error');
            throw new Exception($msg);
        }
        // 将所有数据转为数组
        $data = $activeSheet->toArray(null, true, true, false);
        // 移除表头行
        ArrayHelper::remove($data, 0);
        return $data;
    }

    /**
     * 获取导入数据的字段对应关系
     * @return array
     */
    public static function getImportAttributesMaps()
    {
        $model = new CompanyUpload();
        $attributes = $model->attributeLabels();
        unset($attributes['id']);
        return $attributes;
    }
}
