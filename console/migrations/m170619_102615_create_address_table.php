<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m170619_102615_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->comment('会员ID'),
            'name' => $this->string(10)->comment('收货人')->notNull(),
            'address' => $this->string(50)->comment('地址')->notNull(),
            'tel' => $this->string(20)->comment('电话')->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
