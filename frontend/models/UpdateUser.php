<?php

namespace frontend\models;

use yii\base\Model;

class UpdateUser extends Model
{
    public $username;
    public $old_password;
    public $password;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username','old_password','password'],'required','message'=>'3-20位字符，可由中文、字母、数字和下划线组成'],
            [['username','old_password','password'], 'string'],
//            [['password'],'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => '用户名：',
            'old_password' => '旧密码',
            'password' => '新密码',
        ];
    }
}
