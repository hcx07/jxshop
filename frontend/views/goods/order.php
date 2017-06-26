<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>填写核对订单信息</title>
</head>
<body>
<?php
$this->registerCssFile('@web/style/fillin.css');
$this->registerJsFile('@web/js/cart2.js',['depends'=>\yii\web\JqueryAsset::className()]);
?>
	<div style="clear:both;"></div>
	
	<!-- 页面头部 start -->
	<div class="header w990 bc mt15">
		<div class="logo w990">
			<h2 class="fl"><a href="/shop/index.html"><img src="/images/logo.png" alt="京西商城"></a></h2>
			<div class="flow fr flow2">
				<ul>
					<li>1.我的购物车</li>
					<li class="cur">2.填写核对订单信息</li>
					<li>3.成功提交订单</li>
				</ul>
			</div>
		</div>
	</div>
	<!-- 页面头部 end -->
	
	<div style="clear:both;"></div>

	<!-- 主体部分 start -->
    <?php $form=\yii\widgets\ActiveForm::begin()?>
	<div class="fillin w990 bc mt15">
		<div class="fillin_hd">
			<h2>填写并核对订单信息</h2>
		</div>

		<div class="fillin_bd">
			<!-- 收货人信息  start-->
			<div class="address">
				<h3>收货人信息</h3>
				<div class="address_info">
                    <?php foreach ($address as $dizhi):?>
                        <p>
                        <input type="radio" value="<?=$dizhi->id?>" name="address_id" <?php echo $dizhi->default==1?"checked='checked'":""?>/><?echo $dizhi->name.' '.$dizhi->tel.' '.$dizhi->address?></p>
                    <?php endforeach;?>
				</div>
			</div>
			<!-- 收货人信息  end-->

			<!-- 配送方式 start -->
			<div class="delivery">
				<h3>送货方式 </h3>
				<div class="delivery_select">
					<table>
						<thead>
							<tr>
								<th class="col1">送货方式</th>
								<th class="col2">运费</th>
								<th class="col3">运费标准</th>
							</tr>
						</thead>
						<tbody>
                            <?php foreach (\frontend\models\Order::Delivery() as $k=>$delivery):?>
                                <tr <?php echo $k==0?"class='cur''":"class=''";?> >
                                    <td>
                                        <label><input type="radio" name="delivery_id" value="<?=$delivery['delivery_id']?>" <?php echo $k==0?"checked='checked'":""?> /><?=$delivery['delivery_name']?></label>

                                    </td>
                                    <td>￥<?=$delivery['delivery_price']?></td>
                                    <td>每张订单不满499.00元,运费15.00元, 订单4...</td>
                                </tr>
                            <?endforeach;?>
						</tbody>
					</table>

				</div>
			</div> 
			<!-- 配送方式 end --> 

			<!-- 支付方式  start-->
			<div class="pay">
				<h3>支付方式 </h3>


				<div class="pay_select">
					<table>
                        <?php foreach (\frontend\models\Order::Payment() as $k=>$payment):?>
                            <tr <?php echo $k==0?"class='cur''":"class=''";?>>
                                <td class="col1"><label><input type="radio" name="payment_id" value="<?=$payment['payment_id']?>" <?php echo $k==0?"checked='checked'":""?>/><?=$payment['payment_name']?></label></td>
                                <td class="col2">送货上门后再收款，支持现金、POS机刷卡、支票支付</td>
                            </tr>
                        <?endforeach;?>
					</table>

				</div>
			</div>
			<!-- 支付方式  end-->

			<!-- 商品清单 start -->
			<div class="goods">
				<h3>商品清单</h3>
				<table>
					<thead>
						<tr>
							<th class="col1">商品</th>
							<th class="col3">价格</th>
							<th class="col4">数量</th>
							<th class="col5">小计</th>
						</tr>	
					</thead>
					<tbody>
                    <?php foreach ($cart as $list):?>
                        <tr>
                            <td class="col1"><a href=""><?=\yii\helpers\Html::img('http://admin.jx.com/'.$list->goods->logo)?></a>  <strong><a href=""><?=$list->goods->name?></a></strong></td>
                            <td class="col3">￥<?=$list->goods->shop_price?></td>
                            <td class="col4"> <?=$list->amount?></td>
                            <td class="col5"><span>￥<?=$list->amount*$list->goods->shop_price?></span></td>
                        </tr>
                    <?endforeach;?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="5">
								<ul>
									<li>
										<span><?php
                                            $total=0;
                                            foreach ($cart as $list){
                                                $total+=$list->amount;

                                            }
                                            echo $total;?> 件商品，总商品金额：</span>
										<em>￥<?php
                                            $total=0;
                                            foreach ($cart as $list){
                                                $total+=$list->amount*$list->goods->shop_price;

                                            }
                                            echo $total;?>.00</em>
									</li>
									<li>
										<span>返现：</span>
										<em>-￥240.00</em>
									</li>
									<li>
										<span>运费：</span>
										<em>￥10.00</em>
									</li>
									<li>
										<span>应付总额：</span>
                                        <input type="hidden" name="total" value="<?php
                                        $total=0;
                                        foreach ($cart as $list){
                                            $total+=$list->amount*$list->goods->shop_price;

                                        }
                                        echo $total;?>">
										<em>￥<?php
                                            $total=0;
                                            foreach ($cart as $list){
                                                $total+=$list->amount*$list->goods->shop_price;

                                            }
                                            echo $total;?>.00</em>
									</li>
								</ul>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
			<!-- 商品清单 end -->
		
		</div>

		<div class="fillin_ft">
            <span><?=\yii\bootstrap\Html::submitButton('提交订单');?></span>
<!--			<a href=""><span>提交订单</span></a>-->
			<p>应付总额：<strong>￥<?php
                    $total=0;
                    foreach ($cart as $list){
                        $total+=$list->amount*$list->goods->shop_price;

                    }
                    echo $total;?>.00元</strong></p>
			
		</div>
	</div>
	<!-- 主体部分 end -->
    <?php \yii\widgets\ActiveForm::end();?>

	<div style="clear:both;"></div>
	