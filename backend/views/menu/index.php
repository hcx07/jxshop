
<div><?=\yii\bootstrap\Html::a('添加',['menu/add'],['class'=>'btn btn-info']);?></div>

<table class="table">
    <tr>
        <th>ID</th>
        <th>菜单名</th>
        <th>地址</th>
        <th>父菜单</th>
        <th>排序</th>
        <th>操作</th>
    </tr>
    <?php foreach ($model as $menu):?>
        <tr>
            <td><?=$menu->id?></td>
            <td><?=$menu->label?></td>
            <td><?=$menu->url?></td>
            <td><?=$menu->parent_id?></td>
            <td><?=$menu->sort?></td>
            <td><?=\yii\bootstrap\Html::a('修改',['menu/edit','id'=>$menu->id],['class'=>'btn btn-info btn-xs'])?> <?=\yii\bootstrap\Html::a('删除',['menu/del','id'=>$menu->id],['class'=>'btn btn-warning btn-xs'])?> </td>
        </tr>
    <?php endforeach;?>
</table>
<?= \backend\models\GoLinkPager::widget([
    'pagination' => $page,
    'go' => true,
]); ?>

