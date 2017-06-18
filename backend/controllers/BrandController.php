<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Request;
use yii\web\UploadedFile;
use xj\uploadify\UploadAction;
use crazyfd\qiniu\Qiniu;
use yii\web\User;

class BrandController extends BackendController
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
        return $this->render('index',['brand'=>$brand,'page'=>$page]);
    }

    public function actionAdd(){
        $model=new Brand();
        $model->status=0;
        if($model->load(\Yii::$app->request->post())){
            $yunlogo=\Yii::$app->request->post()['yunlogo'];
            if($model->validate()){
                $model->yunlogo=$yunlogo;
                $model->save();
                \Yii::$app->session->setFlash('success','品牌添加成功！');
                return $this->redirect(['brand/index']);
            }else{
                var_dump($model->getFirstErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionEdit($id){
        $model=Brand::findOne(['id'=>$id]);
        if($model->load(\Yii::$app->request->post())){
            $yunlogo=\Yii::$app->request->post()['yunlogo'];
            if($model->validate()){
                $yunlogo!=null?$model->yunlogo=$yunlogo:'';
                $model->save();
                \Yii::$app->session->setFlash('success','品牌修改成功！');
                return $this->redirect(['brand/index']);
            }else{
                var_dump($model->getFirstErrors());exit;
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
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    //上传图片到七牛云以及本地服务器
                    $ak = 'zt7cYHq5mPq7-UAM8zr7losB03xUBQsOC15KYgKA';
                    $sk = 'h04GEOs7cwKVoA0SjZ5GLF_pT1-QPUjq-qunCUpz';
                    $domain = 'http://or9qoslna.bkt.clouddn.com/';
                    $bucket = 'jxshop';
                    $qiniu = new Qiniu($ak, $sk,$domain, $bucket);
                    $imgurl=$action->getWebUrl();
//                    var_dump($imgurl);exit;
                    $img=\Yii::getAlias('@webroot').$imgurl;
//        $fileName = \Yii::getAlias('@webroot').'/upload/test.png';
                    $qiniu->uploadFile($img,$imgurl);
                    //获取图片在七牛云的地址
                    $url = $qiniu->getLink($imgurl);
                    $action->output['fileUrl_yun'] = $url;
                    $action->output['fileUrl'] = $action->getWebUrl();
//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                },
            ],
        ];
    }
}
