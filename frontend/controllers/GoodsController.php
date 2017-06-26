<?php
namespace frontend\controllers;
use backend\models\Goods;
use backend\models\GoodsCategory;
use frontend\models\Address;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use Yii;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use frontend\models\Cart;

class GoodsController extends Controller
{
    public $layout = 'login';

    public function actionList($id)
    {
        $model = Goods::findAll(['goods_category_id' => $id]);
        $ones = GoodsCategory::findAll(['depth' => 0]);
//        var_dump($model);exit;
        return $this->render('list', ['model' => $model, 'ones' => $ones]);
    }

    public function actionGoods($id)
    {
        $model = Goods::findOne(['id' => $id]);
        $list = GoodsCategory::findOne(['id' => $model->goods_category_id]);
        $cate = GoodsCategory::findOne(['id' => $list->parent_id]);
        $ones = GoodsCategory::findAll(['depth' => 0]);
//        var_dump($list->name);exit;
        return $this->render('/goods/goods', ['model' => $model, 'list' => $list, 'cate' => $cate, 'ones' => $ones]);
    }

    //添加到购物车
    public function actionAdd()
    {
        $goods_id = Yii::$app->request->post('goods_id');
        $amount = Yii::$app->request->post('amount');
        $goods = Goods::findOne(['id' => $goods_id]);
        if ($goods == null) {
            throw new NotFoundHttpException('商品不存在');
        }
        if (Yii::$app->user->isGuest) {
            $cookies = Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if ($cookie == null) {
                $cart = [];
            } else {
                $cart = unserialize($cookie->value);
                //$cart = [2=>10];
            }
            $cookies = Yii::$app->response->cookies;
            if (key_exists($goods->id, $cart)) {
                $cart[$goods_id] += $amount;
            } else {
                $cart[$goods_id] = $amount;
            }
//            $cart = [$goods_id=>$amount];
            $cookie = new Cookie([
                'name' => 'cart', 'value' => serialize($cart)
            ]);
            $cookies->add($cookie);
        } else {
            //获取cookie中的数据，将数据写入数据表
            $cookies = Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if($cookie){
                $cart=unserialize($cookie->value);
//                var_dump(array_keys($cart));exit;
                foreach ($cart as $goods_id=>$num){
//                    var_dump($goods_id,$num);exit;
                    $res=Cart::findOne(['member_id'=>Yii::$app->user->id,'goods_id'=>$goods_id]);
                    if ($res){ //有该商品则同步到数据库
                        $res->amount=$num;
                        $res->save();
                    }else{//没有该商品添加到数据表
                        $model=new Cart();
                        $model->goods_id=$goods_id;
                        $model->amount=$num;
                        $model->member_id=\Yii::$app->user->id;
                        $model->save();
                    }
                }
            }else{
                $res=Cart::findOne(['member_id'=>Yii::$app->user->id,'goods_id'=>$goods_id]);
                if ($res){ //有该商品则同步到数据库
                    $res->amount=$amount;
                    $res->save();
                }else{//没有该商品添加到数据表
                    $model=new Cart();
                    $model->goods_id=$goods_id;
                    $model->amount=$amount;
                    $model->member_id=\Yii::$app->user->id;
                    $model->save();
                }
            }
            Yii::$app->response->cookies->remove('cart');
        }
        return $this->redirect(['goods/cart']);
    }

    //购物车
    public function actionCart()
    {
        if (Yii::$app->user->isGuest) {
            $cookies = Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if ($cookie == null) {
                $cart = [];
            } else {
                $cart = unserialize($cookie->value);
            }
            $models = [];
            foreach ($cart as $good_id => $amount) {
                $goods = Goods::findOne(['id' => $good_id])->attributes;
                $goods['amount'] = $amount;
                $models[] = $goods;
            }
            //var_dump($models);exit;

        } else {
            $cart = ArrayHelper::map(Cart::find()->where(['member_id' => Yii::$app->user->id])->asArray()->all(), 'goods_id', 'amount');
            $models = [];
            //循环获取商品数据，构造购物车需要的格式
            foreach ($cart as $id => $num) {
                $goods = Goods::find()->where(['id' => $id])->asArray()->one();
                $goods['amount'] = $num;
                $models[] = $goods;
            }
        }
//        var_dump($models);exit;
        return $this->render('/goods/cart', ['models' => $models]);
    }

    public function actionUpdateCart()
    {
        $goods_id = Yii::$app->request->post('goods_id');
        $amount = Yii::$app->request->post('amount');
        $goods = Goods::findOne(['id' => $goods_id]);
        if ($goods == null) {
            throw new NotFoundHttpException('商品不存在');
        }
        if (Yii::$app->user->isGuest) {
            //先获取cookie中的购物车数据
            $cookies = Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if ($cookie == null) {
                $cart = [];
            } else {
                $cart = unserialize($cookie->value);
            }
        }else{
            $res=Cart::findOne(['member_id'=>Yii::$app->user->id,'goods_id'=>$goods_id]);
            if($amount==0){
                if($res->load(\Yii::$app->request->post()) && $res->validate()) {
                    $res->delete();
                }

            }else{
                if($res->load(\Yii::$app->request->post()) && $res->validate()) {
                    $res->amount=$amount;
                    $res->save();
                }

            }
        }
    }

    //订单
    public function actionOrder(){
        $address=Address::findAll(['user_id'=>Yii::$app->user->id]);
        $cart=Cart::findAll(['member_id'=>Yii::$app->user->id]);
        $detail=new OrderGoods();
        $model=new Order();
        $post=Yii::$app->request->post();
//        var_dump($post);exit;
        if($post){
            //存储订单表
            if($model->Add()){
                //存储订单详情表
            foreach ($cart as $list){
//            var_dump($list);exit;
                $detail->order_id=Yii::$app->db->getLastInsertID();
//                var_dump($detail->order_id);exit;
                $detail->goods_id=$list->goods_id;
                $detail->goods_name=Goods::findOne(['id'=>$list->goods_id])->name;
                $detail->logo=Goods::findOne(['id'=>$list->goods_id])->logo;
                $detail->price=Goods::findOne(['id'=>$list->goods_id])->shop_price;
                $detail->amount=$list->amount;
                $detail->total=$list->amount*$detail->price;
//                var_dump($detail);exit;
                $re=$detail->save();
            }
            $cart=Cart::deleteAll(['member_id'=>Yii::$app->user->id]);
            return $this->redirect('order-save.html');
            }
        }
//
//        var_dump($model->getFirstErrors());exit;
        return $this->render('order',['address'=>$address,'cart'=>$cart,'model'=>$model]);
    }
    public function actionOrderSave(){
        return $this->render('order-save');
    }
}