<?php

/* @var $this yii\web\View */
/* @var $model app\models\NewsletterForm */
use yii\widgets\ActiveForm;
use yii\helpers\Html;
$this->title = 'Vytvorenie novej skupiny | BP Mailer';
?>

<div class="site-index">

    <h1>Vytvorenie novej skupiny</h1>

    <div class="container">

      <?php
        $form = ActiveForm::begin([
            'id' => 'newgroup-form',
            'options' => ['enctype' => 'multipart/form-data',
                          'class' => 'col-md-6']
        ]) ?>

        <div class="form-group">
            <?= $form->field($model, 'name') ?>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'description')->textArea(['rows' => '3']) ?>
        </div>

        <div class="form-group">
          <?= $form->field($model, 'file')->fileInput() ?>
        </div>  

        <div class="form-group">
            <?= Html::submitButton('Uložiť', ['class' => 'btn btn-primary', 'name' => 'newgroup-button']) ?>
        </div>
        <?php ActiveForm::end() ?>

    </div>
</div>
