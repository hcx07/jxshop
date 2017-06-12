<?php

namespace backend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsIntro;
use yii\web\Controller;
use xj\uploadify\UploadAction;
use crazyfd\qiniu\Qiniu;
use yii\helpers\ArrayHelper;

class GoodsController extends Controller
{
    public function actionIndex()
    {
        if(\Yii::$app->request->post()){
            $keys=\Yii::$app->request->post()['key'];
            //模糊搜索
            $model=Goods::find()->andwhere([
                'or',
                ['like','name',$keys],
                ['like','sn',$keys],
            ])->all();;
//            var_dump($model);exit;
        }else{
            $model=Goods::find()->all();
        }
        return $this->render('index',['model'=>$model]);
    }
    public function actionAdd(){
        $model=new Goods();
        $intro=new GoodsIntro();
        $now=date('Ymd',time());
        $model->status=1;
        $model->is_on_sale=1;
        if($model->load(\Yii::$app->request->post()) && $intro->load(\Yii::$app->request->post())){
            $yunlogo=\Yii::$app->request->post()['yunlogo'];
            if($model->validate() && $intro->validate()){
                $day=GoodsDayCount::findOne(['day'=>$now]);
                $daycount=new GoodsDayCount();
                if($day){
                    $day->count=$day->count+1;
                    $day->save();
                }else{
                    $daycount->day=$now;
                    $daycount->count=1;
                    $daycount->save();
                }
                $model->yunlogo=$yunlogo;
                $model->create_time=time();
                $model->sn=strval($now*100000+GoodsDayCount::findOne(['day'=>$now])->count);
                $x=$model->save();
                $intro->goods_id=$model->id;
                $intro->save();
                \Yii::$app->session->setFlash('success','商品添加成功！');
                return $this->redirect(['goods/index']);
            }else{
                var_dump($model->getFirstErrors());exit;
            }
        }
        $categories = ArrayHelper::merge([['id'=>0,'name'=>'顶级分类','parent_id'=>0]],GoodsCategory::find()->asArray()->all());
        return $this->render('add',['model'=>$model,'intro'=>$intro,'categories'=>$categories]);
    }
    public function actionEdit($id){
        $model=Goods::findOne(['id'=>$id]);
        $intro=GoodsIntro::findOne(['goods_id'=>$id]);
        if($model->load(\Yii::$app->request->post()) && $intro->load(\Yii::$app->request->post())){
            $yunlogo=\Yii::$app->request->post()['yunlogo'];
            if($model->validate() && $intro->validate()){
                $model->yunlogo=$yunlogo;
                $model->save();
                $intro->goods_id=$model->id;
                $intro->save();
                \Yii::$app->session->setFlash('success','商品修改成功！');
                return $this->redirect(['goods/index']);
            }else{
                var_dump($model->getFirstErrors());exit;
            }
        }
        $categories = ArrayHelper::merge([['id'=>0,'name'=>'顶级分类','parent_id'=>0]],GoodsCategory::find()->asArray()->all());
        return $this->render('add',['model'=>$model,'intro'=>$intro,'categories'=>$categories]);
    }
    public function actionDel($id){
        $model=Goods::findOne(['id'=>$id]);
        $model->status=-1;
        $model->save();
//        var_dump($model);exit;
        return $this->redirect(['goods/index']);
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
                },
            ],
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
            ],
        ];
    }
}
