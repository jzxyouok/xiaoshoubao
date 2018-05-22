<?php

namespace console\controllers;

use common\models\elasticsearch\Company;
use yii\console\Controller;

class EsSyncController extends Controller
{
    public function actionCompany()
    {
        /*Company::deleteIndex();
        Company::createIndex();*/
        ini_set('memory_limit', '512M');
        Company::syncDataToElastic(10000);
    }
}