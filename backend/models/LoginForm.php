<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'password' => '密码',
            'rememberMe' => '记住密码',
        ];
    }
    //验证密码  没有用户或者没有密码则返回错误
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    public function login(){
        //1 根据用户名查找用户
        $admin = AdminUser::findOne(['username'=>$this->username]);
        if($admin){
            //2 验证密码
            if(\Yii::$app->security->validatePassword($this->password,$admin->password_hash)){
                //3 登录
                //自动登录
                $duration = $this->rememberMe?7*24*3600:0;
                \Yii::$app->user->login($admin,$duration);
                return true;
            }else{
                $this->addError('password','密码不正确');
            }
        }else{
            $this->addError('username','用户名不存在');
        }
        return false;
    }
    //得到用户名
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = AdminUser::findByUsername($this->username);
        }
        return $this->_user;
    }
}
