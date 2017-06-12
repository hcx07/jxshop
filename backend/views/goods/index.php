
<div><?=\yii\bootstrap\Html::a('添加',['goods/add'],['class'=>'btn btn-info']);?></div>

<div style="float:right;"><?php
    $form=\yii\bootstrap\ActiveForm::begin();
    echo \yii\bootstrap\Html::input('text','key','关键字');
        \yii\bootstrap\ActiveForm::end();?></div>
<br/><br/>
<table class="table">
    <tr>
        <th>ID</th>
        <th>商品名</th>
        <th>货号</th>
        <th>LOGO</th>
        <th>商品分类</th>
        <th>品牌分类</th>
        <th>市场价格</th>
        <th>商品价格</th>
        <th>库存</th>
        <th>是否在售</th>
        <th>状态</th>
        <th>排序</th>
        <th>添加时间</th>
        <th>描述</th>
        <th>操作</th>
    </tr>
    <?php foreach ($model as $goods):?>
        <tr>
            <td><?=$goods->id?></td>
            <td><?=$goods->name?></td>
            <td><?=$goods->sn?></td>
            <td><?=\yii\bootstrap\Html::img($goods->logo,['width'=>35])?></td>
            <td><?=$goods->goodsCategory->name?></td>
            <td><?=$goods->brand->name?></td>
            <td><?=$goods->market_price?></td>
            <td><?=$goods->shop_price?></td>
            <td><?=$goods->stock?></td>
            <td><?=$goods->is_on_sale==1?'在售':'下架';?></td>
            <td><?=$goods->status==1?'正常':'回收站';?></td>
            <td><?=$goods->sort?></td>
            <td><?=date('Y-m-d H:i:s',$goods->create_time);?></td>
            <td><?=\backend\components\Helper::truncate_utf8_string($goods->goodsIntro->content,10)?></td>
            <td><?=\yii\bootstrap\Html::a('修改',['goods/edit','id'=>$goods->id],['class'=>'btn btn-info btn-xs'])?> <?=\yii\bootstrap\Html::a('删除',['goods/del','id'=>$goods->id],['class'=>'btn btn-warning btn-xs'])?></td>
        </tr>
    <?php endforeach;?>
</table>

