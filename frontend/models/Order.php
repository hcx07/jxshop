<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $name
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property string $tel
 * @property integer $delivery_id
 * @property string $delivery_name
 * @property string $delivery_price
 * @property integer $payment_id
 * @property string $payment_name
 * @property integer $total
 * @property integer $status
 * @property string $trade_no
 * @property integer $create_time
 */
class Order extends \yii\db\ActiveRecord
{
    public $address_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['delivery_id', 'address_id','payment_id'], 'required'],
            [['member_id', 'delivery_id', 'payment_id', 'total', 'status', 'create_time'], 'integer'],
            [['delivery_price'], 'number'],
            [['name', 'delivery_name', 'payment_name', 'trade_no'], 'string', 'max' => 50],
            [['province', 'city', 'area'], 'string', 'max' => 20],
            [['address'], 'string', 'max' => 255],
            [['tel'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '用户ID',
            'name' => '收货人',
            'province' => '省',
            'city' => '市',
            'area' => '县',
            'address' => '详细地址',
            'address_id'=>'地址ID',
            'tel' => '电话号码',
            'delivery_id' => '配送方式ID',
            'delivery_name' => '配送方式名称',
            'delivery_price' => '配送方式价格',
            'payment_id' => '支付方式ID',
            'payment_name' => '支付方式名称',
            'total' => '订单金额',
            'status' => '订单状态',
            'trade_no' => '第三方支付交易号',
            'create_time' => '创建时间',
        ];
    }
    public static function Delivery(){
        return $delivery=[
            ['delivery_id'=>1,'delivery_name'=>'普通快递送货上门','delivery_price'=>'10.00'],
            ['delivery_id'=>2,'delivery_name'=>'特快专递','delivery_price'=>'40.00'],
            ['delivery_id'=>3,'delivery_name'=>'加急快递送货上门','delivery_price'=>'30.00'],
            ['delivery_id'=>4,'delivery_name'=>'平邮','delivery_price'=>'50.00'],
        ];
    }
    public static function Payment(){
        return $payment=[
            ['payment_id'=>1,'payment_name'=>'货到付款'],
            ['payment_id'=>2,'payment_name'=>'在线支付'],
            ['payment_id'=>3,'payment_name'=>'上门自提'],
            ['payment_id'=>4,'payment_name'=>'邮局汇款'],
        ];
    }
    public function Add(){
        $model=new self();
        $model->member_id=Yii::$app->user->id;
        $post=Yii::$app->request->post();
//        var_dump($post);exit;
        $model->name=Address::findOne(['id'=>$post['address_id']])->name;
        $model->address=Address::findOne(['id'=>$post['address_id']])->address;
        $model->tel=Address::findOne(['id'=>$post['address_id']])->tel;
        $model->delivery_id=$post['delivery_id'];
        $model->delivery_name=Order::Delivery()[$post['delivery_id']-1]['delivery_name'];
        $model->delivery_price=Order::Delivery()[$post['delivery_id']-1]['delivery_price'];
        $model->payment_id=$post['payment_id'];
        $model->payment_name=Order::Payment()[$post['delivery_id']-1]['payment_name'];
        $model->payment_name=Order::Payment()[$post['delivery_id']-1]['payment_name'];
        $model->total=$post['total'];
        $model->status=1;
        $model->create_time=time();
//            var_dump($model);exit;
        $res=$model->save();
        if($res){
            return true;
        }else{
            return false;

        }
    }
}
