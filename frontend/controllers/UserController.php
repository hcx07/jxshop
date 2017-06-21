<?php

namespace frontend\controllers;

use backend\components\Helper;
use backend\models\GoodsCategory;
use frontend\models\Address;
use frontend\models\Locations;
use frontend\models\LoginForm;
use frontend\models\Member;
use yii\helpers\ArrayHelper;

class UserController extends \yii\web\Controller
{
    public $layout = 'login';
    //用户注册
    public function actionRegister()
    {
        $model = new Member();
//        var_dump(\Yii::$app->request->post());exit;
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
//            var_dump($model->code);exit;
            $model->password_hash=\Yii::$app->security->generatePasswordHash($model->password);
            $model->created_at=time();
            $model->last_login_ip=ip2long(\Yii::$app->request->getUserIP());
            $model->last_login_time=time();
            $model->status=1;
            $this->auth_key = \Yii::$app->security->generateRandomString();
            $model->save(false);
//            var_dump($model->getFirstErrors());exit;
            \Yii::$app->session->setFlash('success','注册成功！');
            return $this->redirect(['index']);
        }

        return $this->render('register',['model'=>$model]);
    }
    //登录
    public function actionLogin()
    {
        if(!\Yii::$app->user->isGuest){
            return $this->goBack();
        }
        $model = new LoginForm();
        if($model->load(\Yii::$app->request->post()) && $model->login()){
            $usr=Member::findOne(['username'=>$model->username]);
            $usr->password=$model->password;
            $usr->last_login_ip=ip2long(\Yii::$app->request->getUserIP());
            $usr->last_login_time=time();
            $usr->save(false);
//            var_dump($usr->getFirstErrors());exit;
            \Yii::$app->session->setFlash('success','登录成功');
            return $this->redirect(['index']);
        }
        return $this->render('login',['model'=>$model]);
    }
    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->redirect(['login']);
    }

    public function actionAddress(){
        $model=new Address();
        $model_all=Address::findAll(['user_id'=>\Yii::$app->user->id]);
        $dizhi=new Locations();
        $province=ArrayHelper::map(Locations::findAll(['parent_id'=>0]),'id','name');
        if($model->load(\Yii::$app->request->post())&&$dizhi->load(\Yii::$app->request->post())){
            if(\Yii::$app->request->post()['default']!=0){
                $all=Address::find()->all();
                foreach ($all as $res){
                    $res->default=0;
                    $res->save();
                }
            }
            if($model->validate()){
                $area=Locations::findOne(['id'=>$dizhi->name]);
                $city=Locations::findOne(['id'=>$area->parent_id]);
                $province=Locations::findOne(['id'=>$city->parent_id]);
                $model->address=$province->name.'-'.$city->name.'-'.$area->name.'-'.$model->address;
                $model->user_id=\Yii::$app->user->id;
                $model->default=1;
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功！');
                return $this->redirect(['address']);
            }
        }
        return $this->render('address',['model'=>$model,'province'=>$province,'dizhi'=>$dizhi,'model_all'=>$model_all]);
    }
    public function actionChild($pid){
        $pid = isset($pid)?$pid-0:0;
        $res=ArrayHelper::map(Locations::findAll(['parent_id'=>$pid]),'id','name','id');
//        var_dump($res);exit;
        return $this->renderPartial('child',['res'=>json_encode($res)]);
    }
    public function actionAdderssedit($id){
        $model=Address::findOne(['id'=>$id]);
        $model_all=Address::findAll(['user_id'=>\Yii::$app->user->id]);
        $dizhi=new Locations();
        $province=ArrayHelper::map(Locations::findAll(['parent_id'=>0]),'id','name');
        if($model->load(\Yii::$app->request->post())&&$dizhi->load(\Yii::$app->request->post())){
            if(\Yii::$app->request->post()['default']!=0){
                $all=Address::find()->all();
                foreach ($all as $res){
                    $res->default=0;
                    $res->save();
                }
            }
            if($model->validate()){
                $area=Locations::findOne(['id'=>$dizhi->name]);
                $city=Locations::findOne(['id'=>$area->parent_id]);
                $province=Locations::findOne(['id'=>$city->parent_id]);
                $model->address=$province->name.'-'.$city->name.'-'.$area->name.'-'.$model->address;
                $model->user_id=\Yii::$app->user->id;
                $model->default=1;
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功！');
                return $this->redirect(['address']);
            }
        }
        return $this->render('address',['model'=>$model,'province'=>$province,'dizhi'=>$dizhi,'model_all'=>$model_all]);
    }
    public function actionAdderssdel($id){
        $model=Address::findOne(['id'=>$id]);
        $model->delete();
        \Yii::$app->session->setFlash('success','删除成功！');
        return $this->redirect(['address']);
    }
    public function actionAdderssdefault($id){
        $model=Address::findOne(['id'=>$id]);
        $all=Address::find()->all();
        foreach ($all as $res){
            $res->default=0;
            $res->save();
        }
        $model->default=1;
//        var_dump($model->default);
        $model->save();
//        var_dump($model->save());exit;
        \Yii::$app->session->setFlash('success','设置成功！');
        return $this->redirect(['address']);
    }
    //商城首页
    public function actionIndex()
    {
        $ones=GoodsCategory::findAll(['depth'=>0]);
        return $this->render('index',['ones'=>$ones]);
    }
}
