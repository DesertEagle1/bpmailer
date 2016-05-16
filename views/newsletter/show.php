<?php

/* @var $this yii\web\View */
/* @var $model app\models\NewsletterForm */
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Prehľad newslettera | BP Mailer';
?>

<div class="site-index">
    <?php
      if (Yii::$app->session->hasFlash('success')){
        echo '<div class="alert alert-success" role="alert">';
        echo Yii::$app->session->getFlash('success');
        echo "</div>";
      }
    ?>
    <h1>Prehľad newslettera</h1>

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

    </div>
</div>
