<?php

namespace backend\controllers;

use yii\filters\AccessControl;

class GoodsDayCountController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
    //权限管理，只有登陆了才能操作
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

}
