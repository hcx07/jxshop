<div><?=\yii\bootstrap\Html::a('添加',['admin-user/add'],['class'=>'btn btn-info'])?></div><br/>
<table class="table">
    <tr>
        <th>ID</th>
        <th>用户名</th>
        <th>最后登陆时间</th>
        <th>最后登陆IP</th>
        <th>操作</th>
    </tr>
    <?php foreach ($model as $admin):?>
        <tr>
            <td><?=$admin->id?></td>
            <td><?=$admin->username?></td>
            <td><?=date('Y-m-d H:i:s',$admin->last_login)?></td>
            <td><?=$admin->last_ip?></td>
            <td><?=\yii\bootstrap\Html::a('修改',['admin-user/edit','id'=>$admin->id],['class'=>'btn btn-info btn-xs'])?> <?=\yii\bootstrap\Html::a('删除',['admin-user/del','id'=>$admin->id],['class'=>'btn btn-warning btn-xs'])?> <?=\yii\bootstrap\Html::a('权限管理',['admin-user/role','id'=>$admin->id],['class'=>'btn btn-danger btn-xs'])?></td>
        </tr>
    <?php endforeach;?>
</table>
