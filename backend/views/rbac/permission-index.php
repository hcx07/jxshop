<h3>权限列表</h3>
<div><?=\yii\bootstrap\Html::a('添加',['rbac/add-permission'],['class'=>'btn btn-info'])?></div><br/>
<table class="table table-responsive table-bordered">
    <tr>
        <th>名称</th>
        <th>描述</th>
        <th>操作</th>
    </tr>
    <?php foreach ($models as $model):?>
        <tr>
            <td><?=$model->name?></td>
            <td><?=$model->description?></td>
            <td><?=\yii\bootstrap\Html::a('修改',['rbac/edit-permission','name'=>$model->name],['class'=>'btn btn-warning btn-xs'])?>
                <?=\yii\bootstrap\Html::a('删除',['rbac/del-permission','name'=>$model->name],['class'=>'btn btn-danger btn-xs'])?></td>
        </tr>
    <?php endforeach;?>
</table>


