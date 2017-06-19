<?php
namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe;
    public $code;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required','message'=>''],
            ['rememberMe', 'boolean'],
            ['code','captcha'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'username' => '用户名：',
            'password' => '密码：',
            'rememberMe' => '记住密码',
            'code' => '验证码：',
        ];
    }

    //用户登录
    public function login(){
        $admin = Member::findOne(['username'=>$this->username]);
        if($admin){
            if(Yii::$app->security->validatePassword($this->password,$admin->password_hash)){
                $duration = $this->rememberMe?7*24*3600:0;
//                Yii::$app->user->login($admin,$duration);
                $a=Yii::$app->user->login($admin,$duration);
//               var_dump(Yii::$app->user->isGuest);exit;
                return true;
            }else{
                $this->addError('password','密码不正确');
            }
        }else{
            $this->addError('username','用户名不存在');
        }
        return false;
    }
}
