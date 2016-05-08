<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
$this->title = 'Odoslať newsletter | BP Mailer';
?>

<div class="site-index">

    <h1>Odoslať newsletter</h1>

    <div class="body-content">
      <h4>Názov newslettera</h4>
      <p><?= $model->subject ?></p>

      <h4>Vytvorený</h4>
      <p><?= $model->created_at ?></p>

      <h4>Počet príjemcov</h4>
      <p><?= $subscribersCount ?></p>

      <h4>Obsah správy</h4>
      <?= $model->content ?>

      <h4>Prílohy</h4>
      <ul>
	      <?php
	      foreach ($attachments as $key => $value) {
	      	echo '<li><a href="files/' . $value['filename_hash'] . '">' . $value['filename'] . "</a></li>";
	      }
	      ?>
  	  </ul>

      <?php
        $form = ActiveForm::begin([
            'id' => 'sendnewsletter-form',
        ]) ?>

        <?= $form->field($modelSend, 'hide')->hiddenInput()->label(false) ?>

        <div class="form-group">
            <?= Html::submitButton('Odoslať', ['class' => 'btn btn-primary', 'name' => 'sendnewsletter-button']) ?>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>
