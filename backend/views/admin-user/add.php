<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;


?>
<div class="site-signup">
    <h1><?= Html::encode('管理员') ?></h1>
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 're_password')->passwordInput() ?>

                <div class="form-group">
                    <?= Html::submitButton('提交', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
