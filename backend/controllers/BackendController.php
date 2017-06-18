<?php
namespace backend\controllers;

use yii\web\Controller;
use backend\components\RbacFilter;

class BackendController extends Controller{
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
            ]
        ];
    }
}