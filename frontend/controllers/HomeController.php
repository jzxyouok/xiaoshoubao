<?php

namespace frontend\controllers;

use frontend\components\AuthWebController;

class HomeController extends AuthWebController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}