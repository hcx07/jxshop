<?php
namespace frontend\controllers;
use backend\models\Goods;
use yii\web\Controller;

class GoodsController extends Controller{
    public $layout = 'login';
    public function actionList($id){
        $model=Goods::findAll(['goods_category_id'=>$id]);
//        var_dump($model);exit;
        return $this->render('list',['model'=>$model]);
    }

}