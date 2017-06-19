<?php

namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\LoginForm;
use frontend\models\Member;

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


    public function actionIndex()
    {

        return $this->render('index');
    }

    public function actionAddress(){
        $model=new Address();
        return $this->render('address',['model'=>$model]);
    }


}
