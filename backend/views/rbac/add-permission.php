<?php
echo '<h3>权限添加/修改</h3>';
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model, 'name');
echo $form->field($model, 'description')->textarea();
echo \yii\bootstrap\Html::submitButton('提交', ['class' => 'btn btn-primary']);
\yii\bootstrap\ActiveForm::end();


