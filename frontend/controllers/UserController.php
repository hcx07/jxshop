<?php

namespace frontend\controllers;

use backend\components\Helper;
use backend\models\GoodsCategory;
use frontend\models\Address;
use frontend\models\Locations;
use frontend\models\LoginForm;
use frontend\models\Member;
use yii\helpers\ArrayHelper;

use Flc\Alidayu\Client;
use Flc\Alidayu\App;
use Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend;
use Flc\Alidayu\Requests\IRequest;

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
            $model->auth_key = \Yii::$app->security->generateRandomString();
            $model->save(false);
//            var_dump($model->getFirstErrors());exit;
            \Yii::$app->session->setFlash('success','注册成功！');
            return $this->redirect(['/shop/index']);
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
            return $this->redirect(['/shop/index']);
        }
        return $this->render('/user/login',['model'=>$model]);
    }
    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->redirect(['/user/login']);
    }

    public function actionAddress(){
        $model=new Address();
        $model_all=Address::findAll(['user_id'=>\Yii::$app->user->id]);
//        var_dump(\Yii::$app->request->post());;exit;
        if($model->load(\Yii::$app->request->post())){
            if(!empty(\Yii::$app->request->post()['default'])){
                $all=Address::find()->all();
                foreach ($all as $res){
                    $res->default=0;
                    $res->save();
                }
                $model->default=1;
            }
            if($model->validate()){
                $model->address=$model['province'].'-'.$model['city'].'-'.$model['county'].'-'.$model->address;
                $model->user_id=\Yii::$app->user->id;
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功！');
                return $this->redirect(['/user/address']);
            }
        }
        return $this->render('address',['model'=>$model,'model_all'=>$model_all]);
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
        if($model->load(\Yii::$app->request->post())){
            if(\Yii::$app->request->post()['default']!=0){
                $all=Address::find()->all();
                foreach ($all as $res){
                    $res->default=0;
                    $res->save();
                }
                $model->default=1;
            }
            if($model->validate()){
                $model->address=$model['province'].'-'.$model['city'].'-'.$model['county'].'-'.$model->address;
                $model->user_id=\Yii::$app->user->id;
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功！');
                return $this->redirect(['/user/address']);
            }
        }
        return $this->render('address',['model'=>$model,'model_all'=>$model_all]);
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

    //测试验证码
    public function actionSend(){
        $config = [
            'app_key'    => '24479112',
            'app_secret' => 'c9a4e23b23113ce97690bbb9af03afe7',
            // 'sandbox'    => true,  // 是否为沙箱环境，默认false
        ];

// 使用方法一
        $code=mt_rand(1000,9999);
        $client = new Client(new App($config));
        $req    = new AlibabaAliqinFcSmsNumSend;

        $req->setRecNum('18380429355')//给谁发验证码
            ->setSmsParam([
                'code' => $code
            ])
            ->setSmsFreeSignName('何长枭')
            ->setSmsTemplateCode('SMS_71510200');

        $resp = $client->execute($req);
        var_dump($resp);
    }
    //短信验证码发送
    public function actionSendSms()
    {
        //确保上一次发送短信间隔超过1分钟
        $tel = \Yii::$app->request->post('tel');
        if(!preg_match('/^1[34578]\d{9}$/',$tel)){
            echo '电话号码不正确';
            exit;
        }
        $code = rand(1000,9999);
        $result = \Yii::$app->sms->setNum($tel)->setParam(['code' => $code])->send();
        if($result){
            //保存当前验证码 session  mysql  redis  不能保存到cookie
//            \Yii::$app->session->set('code',$code);
//            \Yii::$app->session->set('tel_'.$tel,$code);
            \Yii::$app->cache->set('tel_'.$tel,$code,5*60);
            echo 'success'.$code;
        }else{
            echo '发送失败';
        }
    }
    //邮件发送
    public function actionEmail(){
        $result=\Yii::$app->mailer->compose()
            ->setFrom('756170593@163.com')//发送邮件
            ->setTo('756170593@qq.com')//接收邮件
            ->setSubject('邮件测试')//邮件标题
//            ->setTextBody('Plain text content')
            ->setHtmlBody('<b>这是一封测试<a href="http://muniao.org">邮件</a></b>')//以html格式发送邮件
            ->send();
        var_dump($result);


    }
}
