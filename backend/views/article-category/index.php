<div><?=\yii\bootstrap\Html::a('添加',['article-category/add'],['class'=>'btn btn-info'])?></div><br/>
<table class="table">
    <tr>
        <th>ID</th>
        <th>文章分类名名</th>
        <th>简介</th>
        <th>排序</th>
        <th>状态</th>
        <th>帮助文档</th>
        <th>操作</th>
    </tr>
    <?php foreach ($model as $cates):?>
        <tr>
            <td><?=$cates->id?></td>
            <td><?=$cates->name?></td>
            <td><?=$cates->intro?></td>
            <td><?=$cates->sort?></td>
            <td><?php if($cates->status==-1){
                    echo '删除';
                }elseif($cates->status==1){
                    echo '正常';
                }elseif($cates->status==0){
                    echo '隐藏';
                }?></td>
            <td><?=$cates->status==1?'是':'否';?></td>
            <td><?=\yii\bootstrap\Html::a('修改',['article-category/edit','id'=>$cates->id],['class'=>'btn btn-info btn-xs'])?> <?=\yii\bootstrap\Html::a('删除',['article-category/del','id'=>$cates->id],['class'=>'btn btn-warning btn-xs'])?></td>
        </tr>
    <?php endforeach;?>
</table>