<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>WeUI</title>
    <!-- 引入 WeUI -->
    <link rel="stylesheet" href="//res.wx.qq.com/open/libs/weui/1.1.2/weui.min.css"/>
</head>
<body>
<div class="weui-cells__title">订单列表</div>
<?php foreach ($orders as $order):?>
<div class="weui-cells">
    <div class="weui-cell">
        <div class="weui-cell__bd">
            <p>订单编号：<?php echo $order->id?></p>
            <p>收货人：<?php echo $order->name?></p>
            <p>电话：<?php echo $order->tel?></p>
            <p>收货地址：<?php echo $order->address?></p>
            <p>送货名称：<?php echo $order->delivery_name?></p>
        </div>
        <div class="weui-cell__ft"><?php echo $username?></div>
    </div>
</div>
<?php endforeach;?>
</body>
</html>




