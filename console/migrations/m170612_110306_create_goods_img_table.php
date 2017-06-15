<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods_img`.
 */
class m170612_110306_create_goods_img_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('goods_img', [
            'goods_id' => $this->primaryKey(),
            'img' => $this->string(255)->comment('商品图片'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods_img');
    }
}
