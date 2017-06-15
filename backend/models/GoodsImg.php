<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "goods_img".
 *
 * @property integer $goods_id
 * @property string $img
 */
class GoodsImg extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_img';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id'], 'integer'],
            [['path'], 'required'],
            [['path'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => '商品id',
            'path' => '图片地址',
        ];
    }
}
