<?php
namespace frontend\controllers;
use backend\models\GoodsCategory;
use yii\web\Controller;

class ShopController extends Controller{
    public $layout = 'login';
    public function actionIndex()
    {
        $ones=GoodsCategory::findAll(['depth'=>0]);
        return $this->render('index',['ones'=>$ones]);
    }
}
