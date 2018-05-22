<?php

namespace backend\models\form;

use common\models\base\ConfigString;
use common\models\CompanyUpload;
use common\models\JobSchedule;
use console\jobs\CompanyImportJob;
use Yii;
use yii\base\Model;

class CompanyUploadForm extends Model
{
    /**
     * [
     * "path" => "1/3GRkWF-GNSmI_izeHiy37K9dsUv8n4gE.jpg"
     * "name" => "头像.jpg"
     * "size" => "53150"
     * "type" => "image/jpeg"
     * "order" => ""
     * "base_url" => "/uploads"
     * ]
     * @var array
     */
    public $filename;

    public function rules()
    {
        return [
            ['filename', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'filename' => '上传文件'
        ];
    }

    public function import()
    {
        $filename = Yii::getAlias('@webroot' . $this->filename['base_url'] . '/' . $this->filename['path']);
        if (!file_exists($filename)) {
            $this->addError('filename', '文件不存在');
            return false;
        }
        $jobSchedule = JobSchedule::createOne('导入企业信息');
        ConfigString::getQueue()->push(new CompanyImportJob([
            'jobScheduleId' => $jobSchedule->id,
            'filename' => $filename,
            'tableName' => CompanyUpload::tableName()
        ]));
        return true;
    }
}