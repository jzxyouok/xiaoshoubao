<?php

namespace backend\controllers;

use common\models\Area;
use common\models\City;
use yii\base\Controller;
use yii\helpers\Json;

class DependController extends Controller
{
    public function actionCity()
    {
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $provinceId = $parents[0];
                $out = City::findIdNames($provinceId);
                echo Json::encode(['output' => $out, 'selected' => '']);
                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionArea()
    {
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $cityId = $parents[0];
                $out = Area::findIdNames($cityId);
                echo Json::encode(['output' => $out, 'selected' => '']);
                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }
}