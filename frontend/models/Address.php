<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $address
 * @property string $tel
 */
class Address extends \yii\db\ActiveRecord
{
    public $detail;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id','default'], 'integer'],
            [['name', 'address', 'tel'], 'required'],
            [['name'], 'string', 'max' => 10],
            [['address'], 'string', 'max' => 50],
            [['tel'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '会员ID',
            'name' => '收货人',
            'address' => '地址',
            'tel' => '电话',
            'detail' => '详细地址',
            'default' => '设为默认地址',
        ];
    }
}
