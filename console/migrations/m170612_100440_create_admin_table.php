<?php

use yii\db\Migration;

/**
 * Handles the creation of table `admin`.
 */
class m170612_100440_create_admin_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('admin', [
            'id' => $this->primaryKey(),
            'admin_name' => $this->string()->notNull()->comment('用户名'),
            'password'=>$this->string(100)->notNull()->comment('密码'),
            'last_login'=>$this->integer()->notNull()->comment('最后登陆时间'),
            'last_ip'=>$this->integer()->notNull()->comment('最后登陆IP'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('admin');
    }
}
