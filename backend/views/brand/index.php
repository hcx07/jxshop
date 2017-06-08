<div><?=\yii\bootstrap\Html::a('添加',['brand/add'],['class'=>'btn btn-info'])?></div><br/>
<table class="table">
    <tr>
        <th>ID</th>
        <th>品牌名</th>
        <th>简介</th>
        <th>LOGO</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach ($brand as $brands):?>
        <tr>
            <td><?=$brands->id?></td>
            <td><?=$brands->name?></td>
            <td><?=$brands->intro?></td>
            <td><?=\yii\bootstrap\Html::img($brands->logo,['width'=>35])?></td>
            <td><?=$brands->sort?></td>
            <td><?php if($brands->status==-1){
                    echo '删除';
                }elseif($brands->status==1){
                    echo '正常';
                }elseif($brands->status==0){
                    echo '隐藏';
                }?></td>
            <td><?=\yii\bootstrap\Html::a('修改',['brand/edit','id'=>$brands->id],['class'=>'btn btn-info btn-xs'])?> <?=\yii\bootstrap\Html::a('删除',['brand/del','id'=>$brands->id],['class'=>'btn btn-warning btn-xs'])?></td>
        </tr>
    <?php endforeach;?>
</table>