<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Request;
use yii\web\UploadedFile;
use xj\uploadify\UploadAction;

class BrandController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $query=Brand::find();
        $total=$query->count();
        $page=new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>3,
        ]);
        $brand=$query->offset($page->offset)->limit($page->limit)->orderBy(['id'=>SORT_DESC])->all();
//        $model=ArticleCategory::find()->all();
        return $this->render('index',['brand'=>$brand,'page'=>$page]);
    }
    public function actionAdd(){
        $model=new Brand();
        $model->status=0;
        if($model->load(\Yii::$app->request->post())){
//            $model->imgFile=UploadedFile::getInstance($model,'imgFile');
            if($model->validate()){
//                if($model->imgFile){
//                    $filename='/images/brand/'.uniqid().'.'.$model->imgFile->extension;
//                    $model->imgFile->saveAs(\Yii::getAlias('@webroot').$filename,false);
//                    $model->logo =$filename;
////                    var_dump($filename);exit;
//                }
//                var_dump($model->logo);exit;
                $model->save();
                \Yii::$app->session->setFlash('品牌添加成功！');
                return $this->redirect(['brand/index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionEdit($id){
        $model=Brand::findOne(['id'=>$id]);
        if($model->load(\Yii::$app->request->post())){
//            $model->imgFile=UploadedFile::getInstance($model,'imgFile');
            if($model->validate()){
//                if($model->imgFile){
//                    $filename='/images/brand/'.uniqid().'.'.$model->imgFile->extension;
//                    $model->imgFile->saveAs(\Yii::getAlias('@webroot').$filename,false);
//                    $model->logo =$filename;
////                    var_dump($filename);exit;
//                }
//                var_dump($model->logo);exit;
                $model->save();
                \Yii::$app->session->setFlash('品牌修改成功！');
                return $this->redirect(['brand/index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionDel($id){
        $model=Brand::findOne(['id'=>$id]);
        $model->status=-1;
        $model->save();
//        var_dump($model);exit;
        return $this->redirect(['brand/index']);
    }
    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
//                'format' => function (UploadAction $action) {
//                    $fileext = $action->uploadfile->getExtension();
//                    $filename = sha1_file($action->uploadfile->tempName);
//                    return "{$filename}.{$fileext}";
//                },
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['gif','jpg', 'png','bmp'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    $action->output['fileUrl'] = $action->getWebUrl();
                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                },
            ],
        ];
    }
}
