<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\web\Request;
use yii\web\UploadedFile;

class BrandController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $brand=Brand::find()->all();
        return $this->render('index',['brand'=>$brand]);
    }
    public function actionAdd(){
        $model=new Brand();
        $model->status=0;
        if($model->load(\Yii::$app->request->post())){
            $model->imgFile=UploadedFile::getInstance($model,'imgFile');
            if($model->validate()){
                if($model->imgFile){
                    $filename='/images/brand/'.uniqid().'.'.$model->imgFile->extension;
                    $model->imgFile->saveAs(\Yii::getAlias('@webroot').$filename,false);
                    $model->logo =$filename;
//                    var_dump($filename);exit;
                }
//                var_dump($model);exit;
                $model->save();
                \Yii::$app->session->setFlash('品牌添加成功！');
                return $this->redirect(['brand/index']);
            }
        }
        return $this->render('add',['model'=>$model,'logo'=>null]);
    }
    public function actionEdit($id){
        $model=Brand::findOne(['id'=>$id]);
        $logo=$model->logo;
        if($model->load(\Yii::$app->request->post())){
            $model->imgFile=UploadedFile::getInstance($model,'imgFile');
            if($model->validate()){
                if($model->imgFile){
                    $filename='/images/brand/'.uniqid().'.'.$model->imgFile->extension;
                    $model->imgFile->saveAs(\Yii::getAlias('@webroot').$filename,false);
                    $model->logo =$filename;
//                    var_dump($filename);exit;
                }
//                var_dump($model);exit;
                $model->save();
                \Yii::$app->session->setFlash('品牌修改成功！');
                return $this->redirect(['brand/index']);
            }
        }
        return $this->render('add',['model'=>$model,'logo'=>$logo]);
    }
    public function actionDel($id){
        $model=Brand::findOne(['id'=>$id]);
        $model->status=-1;
        $model->save();
//        var_dump($model);exit;
        return $this->redirect(['brand/index']);
    }
}
