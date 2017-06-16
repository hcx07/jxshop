<h3>查看/修改 <?=$username?> 的权限</h3><br/>
<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'role',['inline'=>true])->checkboxList(\backend\models\AdminUser::getRoleOptions());
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();
