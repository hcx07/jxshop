<?php

namespace backend\controllers;

use yii\filters\AccessControl;

class GoodsDayCountController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
