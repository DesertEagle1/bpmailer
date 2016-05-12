<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;

$this->title = 'Úprava šablóny | BP Mailer';
?>

<div class="site-index">

    <h1>Úprava šablóny <?= $template->template_name ?></h1>

    <div class="body-content">
      <?php
        $form = ActiveForm::begin([
            'id' => 'edittemplate-form',
        ]) ?>

        <div class="form-group">
          <?= $form->field($model, 'sourceCode')->widget(CKEditor::className(), [
              'options' => ['rows' => 6],
              'preset' => 'full'
          ])->label(false) ?>
        </div> 

        <div class="form-group">
            <?= Html::submitButton('Uložiť', ['class' => 'btn btn-primary', 'name' => 'editTemplate-button']) ?>
        </div>
      <?php ActiveForm::end() ?>

    </div>
</div>
