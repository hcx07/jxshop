<div><?=\yii\bootstrap\Html::a('添加',['article/add'],['class'=>'btn btn-info'])?></div><br/>
<table class="table">
    <tr>
        <th>ID</th>
        <th>文章名</th>
        <th>简介</th>
        <th>文章分类</th>
        <th>排序</th>
        <th>状态</th>
        <th>创建时间</th>
        <th>内容</th>
        <th>操作</th>
    </tr>
    <?php foreach ($model as $article):?>
        <tr>
            <td><?=$article->id?></td>
            <td><?=$article->name?></td>
            <td><?=$article->intro?></td>
            <td><?=$article->articleCategory->name?></td>
            <td><?=$article->sort?></td>
            <td><?php if($article->status==-1){
                    echo '删除';
                }elseif($article->status==1){
                    echo '正常';
                }elseif($article->status==0){
                    echo '隐藏';
                }?></td>
            <td><?=date('Y-m-d H:i:s',$article->create_time)?></td>
            <td><?=\backend\components\Helper::truncate_utf8_string($article->articleDetail->content,10)?></td>
            <td><?=\yii\bootstrap\Html::a('修改',['article/edit','id'=>$article->id],['class'=>'btn btn-info btn-xs'])?> <?=\yii\bootstrap\Html::a('删除',['article/del','id'=>$article->id],['class'=>'btn btn-warning btn-xs'])?></td>
        </tr>
    <?php endforeach;?>
</table>
<?= \backend\models\GoLinkPager::widget([
    'pagination' => $page,
    'go' => true,
]); ?>