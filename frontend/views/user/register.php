<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>用户注册</title>
</head>
<body>
<!-- 页面头部 start -->
<div class="header w990 bc mt15">
    <div class="logo w990">
        <h2 class="fl"><a href="index.html"><?=\yii\helpers\Html::img('@web/images/logo.png')?></a></h2>
    </div>
</div>
<!-- 页面头部 end -->

<!-- 登录主体部分start -->
<div class="login w990 bc mt10 regist">
    <div class="login_hd">
        <h2>用户注册</h2>
        <b></b>
    </div>
    <div class="login_bd">
        <div class="login_form fl">
            <?php
            $form=\yii\widgets\ActiveForm::begin(
                    ['fieldConfig'=>[
                            'options'=>['tag'=>'li'],
                            'errorOptions'=>['tag'=>'p']
                        ]
                    ]
            );
            echo '<ul>';
            echo $form->field($model,'username')->textInput(['class'=>'txt']);
            echo $form->field($model,'password')->passwordInput(['class'=>'txt']);
            echo $form->field($model,'re_password')->passwordInput(['class'=>'txt']);
            echo $form->field($model,'email')->textInput(['class'=>'txt']);
            echo $form->field($model,'tel')->textInput(['class'=>'txt']);
//            短信验证码
            $button =  \yii\helpers\Html::button('发送验证码',['id'=>'send_sms_button']);
            echo $form->field($model,'smsCode',['options'=>['class'=>'checkcode'],'template'=>"{label}\n{input}&emsp;&emsp;$button\n{hint}\n{error}"])->textInput(['class'=>'txt']);
            //验证码
            echo $form->field($model,'code',['options'=>['class'=>'checkcode']])->widget(yii\captcha\Captcha::className(),['template'=>'{input}{image}']);
            echo '<li>
                     <label for="">&nbsp;</label>
                      <input type="submit" value="" class="login_btn" />
                  </li>';
            echo '</ul>';
            \yii\widgets\ActiveForm::end();
            ?>
        </div>
        <div class="mobile fl">
            <h3>手机快速注册</h3>
            <p>中国大陆手机用户，编辑短信 “<strong>XX</strong>”发送到：</p>
            <p><strong>1069099988</strong></p>
        </div>
    </div>
</div>
<!-- 登录主体部分end -->
</body>
</html>
<?php
/* @var $this \yii\web\View
 */
$url = \yii\helpers\Url::to(['user/send-sms']);
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
    $("#send_sms_button").click(function(){
        //发送验证码按钮被点击时
        //手机号
        var tel = $("#member-tel").val();
        // console.debug(tel);
        //AJAX post提交tel参数到 user/send-sms
        $.post('$url',{tel:tel},function(data){
            console.debug(data);
            if(data == 'success'){
                console.log('短信发送成功');
                alert('短信发送成功');
            }else{
                console.log(data);
            }
        });
    });
JS
));