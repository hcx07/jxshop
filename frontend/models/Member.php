<?php

namespace frontend\models;

use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

class Member extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $password;//密码明文
    public $re_password;
    public $code;//验证码
    public $smsCode;//短信验证码

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'],'required','message'=>'3-20位字符，可由中文、字母、数字和下划线组成'],
            [['username'],'unique'],
            [['last_login_time', 'last_login_ip', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username'], 'string', 'max' => 50],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash', 'password','re_password'], 'string', 'max' => 100],
            [['email'], 'email'],
            [['password'], 'required','message'=>'6-20位字符，可使用字母、数字和符号的组合，不建议使用纯数字、纯字母、纯符号'],
            [['re_password'], 'string','message'=>'请再次输入密码'],
            [['tel'], 'string', 'max' => 11],
            [['re_password'],'compare','compareAttribute'=>'password'],
            ['code','captcha'],
            //验证短信验证码
            ['smsCode','validateSms']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名：',
            'auth_key' => 'Auth Key',
            'password_hash' => '密码',
            'email' => '邮箱：',
            'tel' => '手机号码：',
            'last_login_time' => '最后登录时间',
            'last_login_ip' => '最后登录ip',
            'status' => 'status',
            'created_at' => '添加时间',
            'updated_at' => '修改时间',
            'password' => '密码：',
            're_password' => '确认密码：',
            'code' => '验证码：',
            'smsCode' => '短信验证码：',
        ];
    }

    //验证短信验证码
    public function validateSms()
    {
        //缓存里面没有该电话号码
        $value = Yii::$app->cache->get('tel_'.$this->tel);
//        var_dump($value);exit;
        var_dump($this->smsCode != $value);
        if(!$value || $this->smsCode != $value){
            $this->addError('smsCode','验证码不正确');
        }
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
