<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article`.
 */
class m170608_104289_create_article_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('article', [
            'id' => $this->primaryKey(),
            //id primaryKey name varchar﴾50﴿ 名称 intro text 简介 article_category_id int﴾﴿ 文章分类id sort int﴾11﴿ 排序 status int﴾2﴿ 状态﴾‐1删除 0隐藏 1正常﴿ create_time int﴾11﴿ 创建时间
            'name'=>$this->string(50)->notNull()->comment('文章名称'),
            'intro'=>$this->text()->comment('简介'),
            'article_category_id'=>$this->integer(11)->comment('文章分类id'),
           // $this->addForeignKey('cate_fk_id','article','article_category_id','article_category','id'),
            'sort'=>$this->integer(11)->comment('排序'),
            'status'=>$this->smallInteger(2)->comment('状态'),
            'create_time'=>$this->integer(11)->comment('创建时间'),
        ]);
        yii\db\Migration::addForeignKey('cate_fk_id','article','article_category_id','article_category','id');

    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article');
    }
}
