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
<div class="weui-cells__title">收货地址列表</div>
<?php foreach ($address as $dizhi):?>
    <div class="weui-cells">
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <p>收货人：<?php echo $dizhi->name?></p>
                <p>收货电话：<?php echo $dizhi->tel?></p>
                <p>收货地址：<?php echo $dizhi->address?></p>
                <p>是否默认：<?php echo $dizhi->default==1?'是':'不是'?></p>
            </div>
            <div class="weui-cell__ft"><?php echo $username?></div>
        </div>
    </div>
<?php endforeach;?>
</body>
</html>
