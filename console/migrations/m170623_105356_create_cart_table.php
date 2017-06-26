<?php

use yii\db\Migration;

/**
 * Handles the creation of table `cart`.
 */
class m170623_105356_create_cart_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('cart', [
            'id' => $this->primaryKey(),
//goods_id	int	商品id
            'goods_id'=>$this->integer()->notNull(),
//amount	int	商品数量
            'amount'=>$this->integer()->notNull(),
//member_id	int	用户id
            'member_id'=>$this->integer()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('cart');
    }
}
