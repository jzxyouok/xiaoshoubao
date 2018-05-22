<?php

namespace backend\controllers;

use backend\components\AuthWebController;
use common\models\base\ConfigString;

class FileController extends AuthWebController
{
    public function actions()
    {
        return [
            'upload' => [
                'class' => 'trntv\filekit\actions\UploadAction',
                'fileStorage' => ConfigString::getFileStorage()
            ],
            'delete' => [
                'class' => 'trntv\filekit\actions\DeleteAction',
                'fileStorage' => ConfigString::getFileStorage()
            ],
            'view' => [
                'class' => 'trntv\filekit\actions\ViewAction',
                'fileStorage' => ConfigString::getFileStorage()
            ],
        ];
    }
}