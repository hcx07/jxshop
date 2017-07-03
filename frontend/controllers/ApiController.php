<?php
namespace frontend\controllers;
use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\Goods;
use backend\models\GoodsCategory;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\LoginForm;
use frontend\models\Member;
use frontend\models\Order;
use frontend\models\UpdateUser;
use yii\captcha\Captcha;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\Response;
use yii\web\UploadedFile;

class ApiController extends Controller{
    //关闭csrf跨域验证
    public $enableCsrfValidation=false;
    //将输出结果转化为JSON格式
    public function init()
    {
        \Yii::$app->response->format=Response::FORMAT_JSON;
        parent::init();
    }

    //1.会员
    //会员注册
    public function actionUserRegister(){
        if($post=\Yii::$app->request->post()){
            $member=new Member();
            $member->username = $post['username'];
            $member->password = $post['password'];
            $member->re_password = $post['password'];
            $member->email = $post['email'];
            $member->tel = $post['tel'];
            if($member->validate()){
                $member->save();
                return ['success'=>'true','errorMsg'=>'','data'=>$member->toArray()];
            }
            //验证失败
            return ['success'=>'false','errorMsg'=>$member->getErrors()];
        }
        return ['success'=>'false','errorMsg'=>'请使用post请求'];
    }
    //会员登陆
    public function actionUserLogin(){
        $model = new LoginForm();
        if($post=\Yii::$app->request->post() ){
            $admin=Member::findOne(['username'=>$post['username']]);
            if($admin){
                if(\Yii::$app->security->validatePassword($post['password'],$admin->password_hash)){
                    \Yii::$app->user->login($admin);
                    return ['success'=>'true','errorMsg'=>'','data'=>$admin->toArray()];
                }else{
                    return ['success'=>'false','errorMsg'=>'密码错误'];
                }
            }else{
                return ['success'=>'false','errorMsg'=>'账号错误'];
            }
        }
        return ['success'=>'false','errorMsg'=>'请使用post请求'];

    }

//-修改密码
    public function actionUserUpdate(){
        if($post=\Yii::$app->request->post()){
            $model=new UpdateUser();
            $model->password=$post['password'];
            $model->old_password=$post['old_password'];
            $model->username=$post['username'];
            if($model->validate()){
                $admin=Member::findOne(['username'=>$post['username']]);
                if($admin){
                    if(\Yii::$app->security->validatePassword($post['old_password'],$admin->password_hash)){
                        $admin->password_hash=\Yii::$app->security->generatePasswordHash($post['password']);
                        $admin->save();
                        return ['success'=>'true','errorMsg'=>'','date'=>$admin->toArray()];
                    }
                    return ['success'=>'false','errorMsg'=>'密码错误'];
                }
                return ['success'=>'false','errorMsg'=>'用户名错误'];
            }
            return ['success'=>'false','errorMsg'=>$model->getErrors()];
        }
        return ['success'=>'false','errorMsg'=>'请使用post请求'];

    }

    //-获取当前登录的用户信息
    public function actionUserInfo(){
        if(!\Yii::$app->user->isGuest){
            $member_id=\Yii::$app->user->id;
            $user=Member::findOne(['id'=>$member_id]);
            return ['success'=>'true','errorMsg'=>'','date'=>$user->toArray()];
        }
        return ['success'=>'false','errorMsg'=>'用户未登陆'];
    }
//2.收货地址
//-添加地址
    public function actionAddAddress(){
        if(!\Yii::$app->user->isGuest){
            if($post=\Yii::$app->request->post()){
                $address=new Address();
                $address->address=$post['address'];
                $address->name=$post['name'];
                $address->default=$post['default'];
                $address->user_id=\Yii::$app->user->id;
                $address->tel=$post['tel'];
                $address->province=$post['province'];
                $address->city=$post['city'];
                $address->county=$post['county'];
//                var_dump($address);exit;
                if($address->validate()){
                    $address->save();
                    return ['success'=>'true','errorMsg'=>'','date'=>$address->toArray()];
                }
                return ['success'=>'false','errorMsg'=>$address->getErrors()];
            }
            return ['success'=>'false','errorMsg'=>'请使用post请求'];
        }
        return ['success'=>'false','errorMsg'=>'用户未登陆'];
    }
//-修改地址
    public function actionUpdateAddress(){
        if(!\Yii::$app->user->isGuest){
            if($post=\Yii::$app->request->post()){
                $address=Address::findOne(['id'=>$post['address_id'],'user_id'=>\Yii::$app->user->id]);
                if($address){
                    $address->address=$post['address'];
                    $address->name=$post['name'];
                    $address->default=$post['default'];
                    $address->tel=$post['tel'];
                    $address->province=$post['province'];
                    $address->city=$post['city'];
                    $address->county=$post['county'];
                    if($address->validate()){
                        $address->save();
                        return ['success'=>'true','errorMsg'=>'','date'=>$address->toArray()];
                    }
                    return ['success'=>'false','errorMsg'=>$address->getErrors()];
                }
                return ['success'=>'false','errorMsg'=>'用户无修改此地址权限'];
            }
            return ['success'=>'false','errorMsg'=>'请使用post请求'];
        }
        return ['success'=>'false','errorMsg'=>'用户未登陆'];
    }
//-删除地址
    public function actionDelAddress(){
        if(!\Yii::$app->user->isGuest){
            if($post=\Yii::$app->request->post()){
                $address=Address::findOne(['id'=>$post['address_id'],'user_id'=>\Yii::$app->user->id]);
                if($address){
                    $address->delete();
                    return ['success'=>'true','errorMsg'=>'','date'=>['address_id'=>$post['address_id'],'msg'=>'删除成功']];
                }
                return ['success'=>'false','errorMsg'=>'用户无修改此地址权限'];
            }
            return ['success'=>'false','errorMsg'=>'请使用post请求'];
        }
        return ['success'=>'false','errorMsg'=>'用户未登陆'];
    }
//-地址列表
    public function actionListAddress(){
        if(!\Yii::$app->user->isGuest){
                $address=Address::findOne(['user_id'=>\Yii::$app->user->id]);
                if($address){
                    return ['success'=>'true','errorMsg'=>'','date'=>$address->toArray()];
                }
                return ['success'=>'false','errorMsg'=>'请添加收货地址'];
        }
        return ['success'=>'false','errorMsg'=>'用户未登陆'];
    }


//3.商品分类
//获取所有商品分类
    public function actionGoodsCateList()
    {
        return ['status'=>'1','msg'=>'','data'=>GoodsCategory::find()->asArray()->all()];
    }
    //获取某分类的所有子分类
    public function actionGoodsCateSon()
    {
        if($category_id = \Yii::$app->request->get('id')){
            $goodsCategory = GoodsCategory::findOne(['id'=>$category_id]);
            $lft = $goodsCategory->lft;
            $rgt = $goodsCategory->rgt;
            $tree = $goodsCategory->tree;
            return ['status'=>'1','msg'=>'','data'=>GoodsCategory::find()->where(['>','lft',$lft])->andWhere(['<','rgt',$rgt])->andWhere(['tree'=>$tree])->asArray()->all()];
        }
        return ['status'=>'-1','msg'=>'缺少参数'];
    }
    //获取某分类的父分类
    public function actionGoodsCateParent()
    {
        if($category_id = \Yii::$app->request->get('id')){
            $goodsCategory = GoodsCategory::findOne(['id'=>$category_id]);
            $parent_id = $goodsCategory->parent_id;
            return ['status'=>'1','msg'=>'','data'=>GoodsCategory::find()->where(['id'=>$parent_id])->asArray()->all()];
        }
        return ['status'=>'-1','msg'=>'缺少参数'];
    }
    /**
     * 商品
     */
    //获取某分类下的所有商品
    public function actionGoodsCate()
    {
        if($category_id = \Yii::$app->request->get('id')){
            return ['status'=>'1','msg'=>'','data'=>Goods::find()->where(['goods_category_id'=>$category_id])->asArray()->all()];
        }
        return ['status'=>'-1','msg'=>'缺少参数'];
    }
    //根据品牌获取商品
    public function actionGoodsBrand()
    {
        if($brand_id = \Yii::$app->request->get('id')){
            return ['status'=>'1','msg'=>'','data'=>Goods::find()->where(['brand_id'=>$brand_id])->asArray()->all()];
        }
        return ['status'=>'-1','msg'=>'缺少参数'];
    }
    /**
     * 文章
     */
    //文章分类列表
    public function actionArticleCategoryIndex()
    {
        return ['status'=>'1','msg'=>'','data'=>ArticleCategory::find()->asArray()->all()];
    }
    //根据文章分类获取文章
    public function actionGetArticlesByCategory()
    {
        if($article_category_id = \Yii::$app->request->get('article_category_id')){
            return ['status'=>'1','msg'=>'','data'=>ArticleCategory::findOne(['id'=>$article_category_id])->articles];
        }
        return ['status'=>'-1','msg'=>'缺少参数'];
    }
    //根据文章获取所属分类
    public function actionGetCategoryByArticle()
    {
        if($article_id = \Yii::$app->request->get('article_id')){
            return ['status'=>'1','msg'=>'','data'=>Article::findOne(['id'=>$article_id])->articleCategory];
        }
        return ['status'=>'-1','msg'=>'缺少参数'];
    }
    /**
     * 购物车
     */
    //购物车添加
    public function actionAddCart()
    {
        $request = \Yii::$app->request;
        if($request->isPost){
            //接收数据
            $goods_id = \Yii::$app->request->post('goods_id');
            $amount = \Yii::$app->request->post('amount');
            $goods = Goods::findOne(['id'=>$goods_id]);
            if($goods == null){
                return ['status'=>'-1','msg'=>'商品不存在'];
            }
            //实例化
            $cart = new Cart();
            //判断是否登录，未登录操作cookie,登录操作数据库
            if(\Yii::$app->user->isGuest){
                //先获取cookie中的购物车数据
                $cookies = \Yii::$app->request->cookies;
                $cookie = $cookies->get('cart');
                if($cookie == null){
                    //cookiez中没有购物车的数据
                    $cart = [];
                }else{
                    $cart = unserialize($cookie->value);
                }
                //将商品的id和数量存到cookie中
                $cookies = \Yii::$app->response->cookies;
                //检查购物车中是否有该商品，有，数量累加
                if(key_exists($goods->id,$cart)){
                    $cart[$goods_id] += $amount;
                }else{
                    $cart[$goods_id] = $amount;
                }
                //$cart = [$goods_id=>$amount];
                $cookie = new Cookie([
                    'name'=>'cart','value'=>serialize($cart)
                ]);
                if($cookies->add($cookie)){
                    return ['status'=>'1','msg'=>'购物车添加成功'];
                }
                return ['status'=>'-1','msg'=>'添加失败'];
            }else{
                //已登录，操作数据库
                //得到登录用户的id
                $id = \Yii::$app->user->getId();
                //先获取数据库中的购物车数据
                $model = Cart::findOne(['member_id'=>$id]);
                //var_dump($goods_id);exit;
                if($model==null){
                    //表示数据库还么有该会员的购物车信息，直接新加一条记录
                    if($cart->add($goods_id,$amount,$id)){
                        return ['status'=>'1','msg'=>'添加成功,此为该会员第一条记录'];
                    }
                }else{
                    //表示数据库中有该会员的购物车信息,然后判断该信息中是否有选中加入的商品，有，就修改记录amount+1
                    if($model2 = Cart::findOne(['goods_id'=>$goods_id,'member_id'=>$id])){
                        //表示该会员已添加过该商品，只需要修改纪录即可
                        $model2->amount += $amount;
                        if($model2->save()){
                            return ['status'=>'1','msg'=>'更新会员购物车成功'];
                        }
                        return ['status'=>'-1','msg'=>$model2->getErrors()];
                    }else{
                        //新增
                        if($cart->add($goods_id,$amount,$id)){
                            return ['status'=>'1','msg'=>'添加会员购物车成功'];
                        }
                        return ['status'=>'-1','msg'=>$cart->getErrors()];
                    }
                }


            }
        }

        return ['status'=>'-1','msg'=>'请使用post请求'];
    }
    //修改购物车某商品数量
    public function actionUpdateCart()
    {
        $request = \Yii::$app->request;
        if($request->isPost){
            //接收数据
            $goods_id = $request->post('goods_id');
            $amount = $request->post('amount');
            $goods = Goods::findOne(['id'=>$goods_id]);
            if($goods == null){
                return ['status'=>'-1','msg'=>'商品不存在'];
            }
            //判断是否登录，未登录操作cookie,登录操作数据库
            if(\Yii::$app->user->isGuest){
                //先获取cookie中的购物车数据
                $cookies = \Yii::$app->request->cookies;
                $cookie = $cookies->get('cart');
                if($cookie == null){
                    //cookiez中没有购物车的数据
                    $cart = [];
                }else{
                    $cart = unserialize($cookie->value);
                }
                //将商品的id和数量存到cookie中
                $cookies = \Yii::$app->response->cookies;
                //检查购物车中是否有该商品，有，修改
                if(key_exists($goods->id,$cart)){
                    $cart[$goods_id] = $amount;
                }else{
                    return ['status'=>'-1','msg'=>'购物车中无该商品'];
                }
                if($amount){
                    $cart[$goods_id] = $amount;
                }else{
                    if(key_exists($goods['id'],$cart)){
                        unset($cart[$goods_id]);
                    }
                }
                $cookie = new Cookie([
                    'name'=>'cart','value'=>serialize($cart)
                ]);
                if($cookies->add($cookie)){
                    return ['status'=>'1','msg'=>'更新数量成功'];
                }
            }else{
                //已登录，操作数据库
                $id = \Yii::$app->user->getId();
                $model = Cart::findOne(['goods_id'=>$goods_id,'member_id'=>$id]);
                if($model == null){
                    return ['status'=>'-1','msg'=>'商品不存在'];
                }
                if($amount){
                    $model->amount = $amount;
                    $model->save();
                }else{
                    //删除数据库中对应的购物车记录
                    $model->delete();
                }
                return ['status'=>'1','msg'=>'会员购物车更新成功'];

            }
        }
    }
    //删除购物车某商品
    public function actionDeleteCart()
    {
        if($goods_id = \Yii::$app->request->get('goods_id')){
            //判断是否登录，未登录操作cookie,登录操作数据库
            if(\Yii::$app->user->isGuest){
                //先获取cookie中的购物车数据
                $cookies = \Yii::$app->request->cookies;
                $cookie = $cookies->get('cart');
                if($cookie == null){
                    //cookiez中没有购物车的数据
                    $cart = [];
                }else{
                    $cart = unserialize($cookie->value);
                }
                //将商品的id和数量存到cookie中
                $cookies = \Yii::$app->response->cookies;
                //检查购物车中是否有该商品，有，修改
                if(key_exists($goods_id,$cart)){
                    unset($cart[$goods_id]);
                    return ['status'=>'1','msg'=>'删除成功','data'=>Goods::findOne(['id'=>$goods_id])->toArray()];
                }else{
                    return ['status'=>'-1','msg'=>'购物车中无该商品'];
                }
            }else{
                //已登录，操作数据库
                $id = \Yii::$app->user->getId();
                $model = Cart::findOne(['goods_id'=>$goods_id,'member_id'=>$id]);
                if($model == null){
                    return ['status'=>'-1','msg'=>'商品不存在'];
                }

                if($model->delete()){
                    return ['status'=>'1','msg'=>'会员购物车商品删除成功'];
                }
                return ['status'=>'-1','msg'=>$model->getErrors()];
            }
        }
        return ['status'=>'-1','msg'=>'缺少参数'];
    }
    //清空购物车
    public function actionCleanCart()
    {
        //判断是否登录，未登录操作cookie,登录操作数据库
        if(\Yii::$app->user->isGuest){
            //先获取cookie中的购物车数据
            $cookies = \Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if($cookie){
                \Yii::$app->response->cookies->remove($cookie);
                return ['status'=>'1','msg'=>'清空购物车成功'];
            }
        }else{
            //已登录，操作数据库
            $id = \Yii::$app->user->getId();
            if($models = Cart::deleteAll(['member_id'=>$id])){
                return ['status'=>'1','msg'=>'清空会员购物车成功'];
            }

            return ['status'=>'-1','msg'=>'清空失败'];

        }

    }
    //获取购物车所有商品
    public function actionCartIndex()
    {
        if(\Yii::$app->user->isGuest){
            //取出cookie中的商品id和数量
            $cookies = \Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if($cookie==null){
                $cart = [];
            }else{
                $cart = unserialize($cookie->value);
            }
            $models = [];
            foreach ($cart as $goods_id => $amount){
                $goods = Goods::findOne(['id'=>$goods_id])->attributes;
                $goods['amount'] = $amount;
                $models[] = $goods;
            }
        }else{
            $id = \Yii::$app->user->getId();
            $models = [] ;
            //数据库中获取购物车数据
            $cart = Cart::find()->select('*')->where(['member_id'=>$id])->asArray()->all();
            // var_dump($cart);exit;
            foreach ($cart as $v){
                $goods = Goods::findOne(['id'=>$v['goods_id']])->attributes;
                $goods['amount']=$v['amount'];
                $models[] = $goods;

            }
        }

        return ['status'=>'1','msg'=>'','data'=>$models];
    }
    /**
     * 订单
     */
    //获取支付方式
    public function actionGetPayment()
    {
        return ['status'=>'1','msg'=>'','data'=>Order::$payment];
    }
    //获取送货方式
    public function actionGetDelivery()
    {
        return ['status'=>'1','msg'=>'','data'=>Order::$delivery];
    }
    //提交订单
    public function actionAddOrder()
    {
        if(\Yii::$app->user->isGuest){
            return ['status'=>'-1','msg'=>'请先登录'];
        }
        $request = \Yii::$app->request;
        if($request->isPost){
            $id = \Yii::$app->user->getId();
            //商品清单
            $models = [] ;
            //数据库中获取购物车数据
            $cart = Cart::find()->select('*')->where(['member_id'=>$id])->asArray()->all();
            // var_dump($cart);exit;
            foreach ($cart as $v){
                $goods = Goods::findOne(['id'=>$v['goods_id']])->attributes;
                $goods['amount']=$v['amount'];
                $models[] = $goods;

            }

            $order = new Order();
            $address_id = $request->post('address_id');
            //查询收货信息
            $address_info = Address::find()->where(['id'=>$address_id])->asArray()->all()[0];
            //var_dump($address_info);exit;

            $order->name = $address_info['name'];
            $order->add_name = $address_info['add_name'];
            $order->tel = $address_info['tel'];
            //查询配送方式
            $delivery_id = $request->post('delivery_id');
            $order->delivery_id = $delivery_id;
            foreach (Order::$delivery as $v){
                if(($v['id']) == $delivery_id){
                    $order->delivery_name = $v['name'];
                    $order->delivery_price = $v['price'];
                }
            }
            //支付方式
            $payment_id = $request->post('payment_id');
            $order->payment_id = $payment_id;
            foreach (Order::$payment as $v){
                if($v['id'] == $payment_id){
                    $order->payment_name = $v['name'];
                }
            }
            $total_decimal=$request->post('total_decimal');
            //总额
            $order->total_decimal = $total_decimal;
            //默认状态为待付款
            $order->status = 1;
            //创建时间
            $order->create_time = time();
            $order->member_id = $id;
            if($order->save() && Cart::deleteAll(['member_id'=>$id])){
                return ['status'=>'1','msg'=>'订单生成成功'];
            }
            /**
             * ---------order-goods 表
             */
            return ['status'=>'-1','msg'=>$order->getErrors()];
        }
    }

    //获取当前用户订单列表
    public function actionGetCurrentUserOrder()
    {
        if(\Yii::$app->user->isGuest){
            return ['status'=>'-1','msg'=>'请先登录'];
        }
        $id = \Yii::$app->user->getId();
        return ['status'=>'1','msg'=>'','data'=>Member::findOne(['id'=>$id])->orders];
    }
    //取消订单
    public function actionCancelOrder()
    {
        if(\Yii::$app->user->isGuest){
            return ['status'=>'-1','msg'=>'请先登录'];
        }
        $id = \Yii::$app->user->getId();
        if($order_id = \Yii::$app->request->get('order_id')){
            $order = Order::findOne(['id'=>$order_id,'member_id'=>$id]);
            if($order == null){
                return ['status'=>'-1','msg'=>'订单不存在'];
            }
            $order->status = 0;
            if($order->save()){
                return ['status'=>'1','msg'=>'取消成功','data'=>$order->toArray()];
            }
            return ['status'=>'-1','msg'=>'','data'=>$order->getErrors()];
        }
        return ['status'=>'-1','msg'=>'缺少参数'];
    }


//高级api
//-验证码
    //return captcha 相当于控制器actionCaptcha
    public function  actions(){
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength'=>4,
                'maxLength'=>4,
            ],
        ];
    }
    //http://www.yii2shop.com/api/captcha.html 显示验证码
    //http://www.yii2shop.com/api/captcha.html?refresh=1 获取新验证码图片地址
    //http://www.yii2shop.com/api/captcha.html?v=59573cbe28c58 新验证码图片地址

//-文件上传
    public function actionUpload()
    {
        $img = UploadedFile::getInstanceByName('img');
        if($img){
            $fileName = '/upload/'.uniqid().'.'.$img->extension;
            $result = $img->saveAs(\Yii::getAlias('@webroot').$fileName,0);
            if($result){
                return ['status'=>'1','msg'=>'','data'=>$fileName];
            }
            return ['status'=>'-1','msg'=>$img->error];
        }
        return ['status'=>'-1','msg'=>'没有文件上传'];
    }
//-分页读取数据
    public function actionPage(){
        //当前页
        \Yii::$app->request->get('page')==null?$page=1:$page=\Yii::$app->request->get('page');
        //        //每页显示条数
        \Yii::$app->request->get('re_page')==null?$re_page=3:$re_page=\Yii::$app->request->get('re_page');
        $query = Goods::find();
        $total = $query->count();//总条数
        $pages = new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>$re_page,
        ]);
        $books = $query->offset($pages->offset)->limit($pages->limit)->orderBy(['id' => SORT_DESC])->all();
//        var_dump($books);exit;
        return ['status'=>'1','msg'=>'','data'=>$books];
    }//-发送手机验证码
    public function actionSms(){
        $tel = \Yii::$app->request->post('tel');
        if(!preg_match('/^1[34578]\d{9}$/',$tel)){
            return ['status'=>0,'msg'=>'电话号码错误'];
        }
        $time=time()-\Yii::$app->cache->get('tel_time_'.$tel);
        if($time<60){
            return ['status'=>0,'msg'=>'请于'.(60-$time).'秒后再试'];
        }
        $code = rand(1000,9999);
        $result = \Yii::$app->sms->setNum($tel)->setParam(['code' => $code])->send();
        if($result){
            //保存当前验证码 session  mysql  redis  不能保存到cookie
//            \Yii::$app->session->set('code',$code);
//            \Yii::$app->session->set('tel_'.$tel,$code);
            \Yii::$app->cache->set('tel_'.$tel,$code,5*60);
            \Yii::$app->cache->set('tel_time_'.$tel,time(),5*60);
            return ['status'=>1,'msg'=>'发送成功','date'=>['验证码是'=>$code]];
        }else{
            return ['status'=>0,'msg'=>'发送失败'];
        }
    }
}
