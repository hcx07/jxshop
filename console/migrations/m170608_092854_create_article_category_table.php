<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article_category`.
 */
class m170608_092854_create_article_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('article_category', [
            'id' => $this->primaryKey(),
//            name varchar﴾50﴿ 名称
            'name'=>$this->string(50)->notNull()->comment('文章分类名'),
//intro text 简介
            'intro'=>$this->text()->comment('简介'),
//sort int﴾11﴿ 排序
            'sort'=>$this->integer(11)->defaultValue(998)->comment('排序号'),
//status int﴾2﴿ 状态﴾‐1删除 0隐藏 1正常﴿
            'status'=>$this->integer(2)->comment('状态'),
//is_help int﴾1﴿ 类型
            'is_help'=>$this->integer(1)->defaultValue(0)->comment('是帮助文档？'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article_category');
    }
}
