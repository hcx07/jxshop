<?php
echo '<h3>角色添加/修改</h3>';
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
echo $form->field($model,'description')->textarea();
//角色的权限
echo $form->field($model,'permissions',['inline'=>true])->checkboxList(\backend\models\RoleForm::getPermissionOptions());
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();