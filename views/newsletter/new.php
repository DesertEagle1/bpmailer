<?php

/* @var $this yii\web\View */
/* @var $model app\models\NewsletterForm */
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\models\NewsletterForm;
use dosamigos\ckeditor\CKEditor;


$this->title = 'Nový newsletter | BP Mailer';
?>

<div class="site-index">

    <h1>Vytvorenie nového newslettera</h1>


    <div class="body-content">
        <?php
        $form = ActiveForm::begin([
            'id' => 'newsletter-form',
            'options' => ['enctype' => 'multipart/form-data']
        ]) ?>

        <div class="form-group">
            <?= $form->field($model, 'subject') ?>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'receivers')->dropdownList($groups,['prompt'=>'Vyberte skupinu odberateľov']) ?>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'copyTo')->hint('Vložte platné e-mailové adresy oddelené čiarkou, napr: priklad1@mail.com,priklad2@mail.com') ?>
        </div>

        <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <?= $form->field($model, 'sentFrom') ?>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <?= $form->field($model, 'replyTo') ?>
              </div>
            </div>
          </div>

          <div class="form-group">
            <?= $form->field($model, 'template')->dropdownList($templates,['prompt'=>'Vyberte šablónu']) ?>
          </div>

          <div class="form-group">
            <?= $form->field($model, 'content')->widget(CKEditor::className(), [
                'options' => ['rows' => 6],
                'preset' => 'full',
            ])->label(false) ?>
          </div>

          <div class="form-group">
            <?= $form->field($modelUpload, 'attachments[]')->fileInput(['multiple' => true]) ?>
          </div>  

        <div class="form-group">
            <?= Html::submitButton('Uložiť', ['class' => 'btn btn-primary', 'name' => 'saveNewsletter-button']) ?>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $("#newsletterform-template").change(function() {
      //$("#newsletterform-content").val("something");
      //console.log($("#newsletterform-content"));
      //var editor = document.getElementById('newsletterform-content');
      //editor.innerHTML = "something";
      //console.log(editor);
    })
  });
</script>
