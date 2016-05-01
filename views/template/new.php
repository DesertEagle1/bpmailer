<?php

/* @var $this yii\web\View */
/* @var $model app\models\NewsletterForm */
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use dosamigos\ckeditor\CKEditor;

$this->title = 'Vytvorenie novej šablóny | BP Mailer';
?>

<div class="site-index">

    <h1>Vytvorenie novej šablóny</h1>


    <div class="container">
      <?php
        $form = ActiveForm::begin([
            'id' => 'newtemplate-form',
        ]) ?>

        <div class="form-group">
            <?= $form->field($model, 'name') ?>
        </div>

        <div class="form-group">
          <?= $form->field($model, 'sourceCode')->widget(CKEditor::className(), [
              'options' => ['rows' => 6],
              'preset' => 'full'
          ])->label(false) ?>
        </div> 

      <div class="form-group">
          <?= Html::submitButton('Uložiť', ['class' => 'btn btn-primary', 'name' => 'saveTemplate-button']) ?>
      </div>
        <?php ActiveForm::end() ?>
    </div>
</div>
