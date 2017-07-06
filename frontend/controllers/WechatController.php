<?php
namespace frontend\controllers;
use backend\models\Goods;
use EasyWeChat\Message\News;
use frontend\models\Address;
use frontend\models\CURL;
use frontend\models\Member;
use frontend\models\Order;
use yii\helpers\Url;
use yii\web\Controller;
use EasyWeChat\Foundation\Application;

class WechatController extends Controller{
    public $enableCsrfValidation=false;
    public function actionIndex(){
        $app = new Application(\Yii::$app->params['wechat']);
        $server = $app->server;
        $server->setMessageHandler(function ($message) {
            switch ($message->MsgType) {
                case 'text':
                    switch ($message->Content){
                        case '注册':
                            $url = Url::to(['user/register'],true);
                            return '点此注册：'.$url;
                            break;
                        case '优惠':
                            $model=Goods::find()->asArray()->all();
                            $keys=array_rand($model,5);
//        var_dump($keys);exit;
                            foreach ($keys as $good){
                                $new = new News([
                                    'title'       => $model[$good]['name'],
                                    'description' => $model[$good]['name'],
                                    'url'         => 'http://jxshop.muniao.org/goods/goods.html?id='.$model[$good]['id'],
                                    'image'       => 'http://jxadmin.muniao.org/'.$model[$good]['logo'],
                                ]);
                                $news[]=$new;
                            }
                            return $news;
                            break;
                        case '帮助':
                            return '您可以发送 优惠、解除绑定 等信息';
                            break;
                        case '解除绑定':
                            $openid = \Yii::$app->session->get('openid');
                            if($openid==null){
                                return '用户暂未绑定';
                            }else{
                                $model=Member::findOne(['openid'=>$openid]);
                                $model->openid='';
                                $model->save();
                                return '解绑成功';
                            }
                            break;
                        default :
                            $url='http://www.tuling123.com/openapi/api?key=5d1042df188b4fe689bb1abc557bb961&info='.$message->Content;
                            $model=new CURL();
                            $r = $model->requestPost($url,false);
                            return json_decode($r)->text;
                            break;
                    }
//                    return $message->Content;
//                    break;
                case 'event':
                    //事件的类型   $message->Event
                    //事件的key值  $message->EventKey
                    if($message->EventKey == 'Promotion'){//菜单点击事件
                        $model=Goods::find()->asArray()->all();
                        $keys=array_rand($model,5);
//        var_dump($keys);exit;
                        foreach ($keys as $good){
                            $new = new News([
                                'title'       => $model[$good]['name'],
                                'description' => $model[$good]['name'],
                                'url'         => 'http://jxshop.muniao.org/goods/goods.html?id='.$model[$good]['id'],
                                'image'       => 'http://jxadmin.muniao.org/'.$model[$good]['logo'],
                            ]);
                            $news[]=$new;
                        }
                        return $news;
                        break;
                    }
            }
        });
        $response = $app->server->serve();
// 将响应输出
        $response->send();
    }

    //设置菜单
    public function actionSetMenu()
    {
        $app = new Application(\Yii::$app->params['wechat']);
        $menu = $app->menu;
        $buttons = [
            [
                "type" => "click",
                "name" => "促销商品",
                "key"  => "Promotion"
            ],
            [
                "type" => "view",
                "name" => "在线商城",
                "url"  => "http://www.jx.com/shop/index.html"
            ],
            [
                "name"       => "个人中心",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "绑定账户",
                        "url" => Url::to(['wechat/login'],true)
                    ],
                    [
                        "type" => "view",
                        "name" => "我的订单",
                        "url"  => Url::to(['wechat/order'],true)
                    ],
                    [
                        "type" => "view",
                        "name" => "收货地址",
                        "url" => Url::to(['wechat/address'],true)
                    ],
                    [
                        "type" => "view",
                        "name" => "修改密码",
                        "url" => Url::to(['wechat/update'],true)
                    ],
                ],
            ],
        ];
        $menu->add($buttons);
        //获取已设置的菜单（查询菜单）
        $menus = $menu->all();
        var_dump($menus);
    }
    //我的订单
    public function actionOrder()
    {
        //openid
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            $app = new Application(\Yii::$app->params['wechat']);
            $response = $app->oauth->scopes(['snsapi_base'])
                ->redirect();
            $response->send();
        }
        //通过openid获取账号
        $member = Member::findOne(['openid'=>$openid]);
        $username=$member->username;
        if($member == null){
            return $this->redirect(['wechat/login']);
        }else{
            //已绑定账户
            $orders = Order::findAll(['member_id'=>$member->id]);
            return $this->renderPartial('order',['orders'=>$orders,'username'=>$username]);
        }
    }

    //收货地址
    public function actionAddress(){
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            $app = new Application(\Yii::$app->params['wechat']);
            $response = $app->oauth->scopes(['snsapi_base'])
                ->redirect();
            $response->send();
        }else{
            //通过openid获取账号
            $member = Member::findOne(['openid'=>$openid]);
            $username=$member->username;
            if($member == null){
                return $this->redirect(['wechat/login']);
            }else{
                //已绑定账户
                $address = Address::findAll(['user_id'=>$member->id]);
                return $this->renderPartial('address',['address'=>$address,'username'=>$username]);
            }
        }

    }

    //修改密码
    public function actionUpdate(){
        $openid = \Yii::$app->session->get('openid');
        $post=\Yii::$app->request->post();
        if($post){
            if($post['new_password']==$post['re_password']){
                $user=Member::findOne(['openid'=>$openid]);
                if($user){
                    $res=\Yii::$app->security->validatePassword($post['old_password'],$user->password_hash);
                    if($res){
                        $user->password_hash=\Yii::$app->security->generatePasswordHash($post['new_password']);
                        $user->save();
                        return '密码修改成功';
                    }
                    return '旧密码输入错误';
                }
                return '用户不存在';
            }
            return '两次输入密码不一致';
        }
        if($openid == null){
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            $app = new Application(\Yii::$app->params['wechat']);
            $response = $app->oauth->scopes(['snsapi_base'])
                ->redirect();
            $response->send();
        }
        //通过openid获取账号
        $member = Member::findOne(['openid'=>$openid]);
        if($member == null){
            return $this->redirect(['wechat/login']);
        }else{
            return $this->renderPartial('update',['username'=>$member->username]);
        }
    }

    //个人中心
    public function actionUser()
    {
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            //wechat/user  \Yii::$app->controller->action->uniqueId
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            //echo 'wechat-user';
            $app = new Application(\Yii::$app->params['wechat']);
            //发起网页授权
            $response = $app->oauth->scopes(['snsapi_base'])
                ->redirect();
            $response->send();
        }
        var_dump('openid是：'.$openid);
    }

    //授权回调页
    public function actionCallback()
    {
        $app = new Application(\Yii::$app->params['wechat']);
        $user = $app->oauth->user();
        // $user 可以用的方法:
        // $user->getId();  // 对应微信的 OPENID
        // $user->getNickname(); // 对应微信的 nickname
        // $user->getName(); // 对应微信的 nickname
        // $user->getAvatar(); // 头像网址
        // $user->getOriginal(); // 原始API返回的结果
        // $user->getToken(); // access_token， 比如用于地址共享时使用
//        var_dump($user->getId());
        //将openid放入session
        \Yii::$app->session->set('openid',$user->getId());
        return $this->redirect([\Yii::$app->session->get('redirect')]);

    }

    public function actionDel()
    {
        \Yii::$app->session->removeAll();
        echo '删除所有session成功';
    }


    //绑定用户账号
    public function actionLogin()
    {
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            $app = new Application(\Yii::$app->params['wechat']);
            $response = $app->oauth->scopes(['snsapi_base'])
                ->redirect();
            $response->send();
        }
        //登录，成功将openid写入当前登录账户
        $request = \Yii::$app->request;
        if(\Yii::$app->request->isPost){
            $user = Member::findOne(['username'=>$request->post('username')]);
            if($user && \Yii::$app->security->validatePassword($request->post('password'),$user->password_hash)){
                \Yii::$app->user->login($user);
                $user->openid=$openid;
                $user->save();
                if(\Yii::$app->session->get('redirect')){
                    return $this->redirect([\Yii::$app->session->get('redirect')]);
                }
                echo '绑定成功';exit;
            }else{
                echo '登录失败';exit;
            }
        }

        return $this->renderPartial('login');
    }
    public function actionTest(){
//        $model=Goods::find()->asArray()->all();
//        $keys=array_rand($model,5);
////        var_dump($keys);exit;
//        foreach ($keys as $good){
//            $new = new News([
//                'title'       => $model[$good]['name'],
//                'description' => $model[$good]['name'],
//                'url'         => 'http://jxshop.muniao.org/goods/goods.html?id='.$model[$good]['id'],
//                'image'       => 'http://jxadmin.muniao.org/'.$model[$good]['logo'],
//            ]);
//            $news[]=$new;
//        }
//        var_dump($news);

        $openid = \Yii::$app->session->get('openid');
        var_dump($openid);
    }
}
