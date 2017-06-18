<?php

namespace backend\controllers;

use yii\filters\AccessControl;

class GoodsDayCountController extends BackendController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
