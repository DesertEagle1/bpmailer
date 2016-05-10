<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Zmena hesla | BP Mailer';

?>

<div class="container">
    <h1>Zmena hesla</h1>
    <div class="site-changepassword">
        <?php
            $form = ActiveForm::begin([
                'id' => 'changepassword-form',
                'options' => ['class' => 'col-md-6']
            ]) ?>

            <div class="form-group">
                <?= $form->field($model, 'oldPassword')->passwordInput() ?>
            </div>

            <div class="form-group">
                <?= $form->field($model, 'newPassword')->passwordInput() ?>
            </div>

            <div class="form-group">
                <?= $form->field($model, 'newPasswordRepeat')->passwordInput() ?>
            </div>  

            <div class="form-group">
                <?= Html::submitButton('Zmeniť heslo', ['class' => 'btn btn-primary', 'name' => 'changepassword-button']) ?>
            </div>
        <?php ActiveForm::end() ?>
      </div>
    </div>
</div>