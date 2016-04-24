<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LoginForm */

if (Yii::$app->user->isGuest){
  $this->title = 'Prihlásenie | BP Mailer';
}
else
{
  $this->title = 'Prehľad | BP Mailer';
}
?>

<div class="container">
  <?php
  if (Yii::$app->user->isGuest){
    ?>
    <div class="site-login">
      <div class="row">
          <div class="col-lg-5">
              <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                  <?= $form->field($model, 'username')->label('Prihlasovacie meno')->textInput(['autofocus' => true]) ?>

                  <?= $form->field($model, 'password')->label('Heslo')->passwordInput() ?>

                  <?= $form->field($model, 'rememberMe')->checkbox(['label'=>'Pamätať si ma']) ?>

                  <div class="form-group">
                      <?= Html::submitButton('Prihlásiť', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                  </div>

              <?php ActiveForm::end(); ?>
          </div>
      </div>
    </div>
  <?php  
  }

  else 

  {
  ?>
  <h1>Prehľad noviniek</h1>
    <div class="jumbotron">
      
      <div class="row">
        <div class="col-lg-4"><img src="img/graf01.png" alt="graf 1"></div>
        <div class="col-lg-4"><img src="img/graf02.png" alt="graf 2"></div>
        <div class="col-lg-4"><img src="img/graf02.png" alt="graf 2"></div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-6">
        <h2>Najnovšie newslettere</h2>
        <p class="text-right"><a class="btn btn-primary" href="newsletter.html" role="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Vytvoriť nový newsletter</a></p>
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Dátum</th>
              <th>Názov newslettera</th>
              <th>Počet odberateľov</th>
              <th>Úspešnosť</th>
            </tr>
          </thead>

          <tbody>
            <tr>
              <td>3.1.2016</td>
              <td>Výpredaj notebookov</td>
              <td>914</td>
              <td>59,6%</td>
            </tr>

            <tr>
              <td>2.1.2016</td>
              <td>Nové smartfóny</td>
              <td>1103</td>
              <td>63%</td>
            </tr>

            <tr>
              <td>1.1.2016</td>
              <td>Späť do školy</td>
              <td>677</td>
              <td>51%</td>
            </tr>
          </tbody>  

        </table>
        <p><a href="?r=site%2Fcampaigns">Zoznam všetkých newsletterov</a></p>
      </div>
      <div class="col-lg-6">
        <h2>Dnešná aktivita (1.1.2016)</h2>
        
        <h3>Noví odberatelia</h3>
        <p>Dnes pribudlo <strong>8</strong> nových odberateľov: address1@mail.com, address2@mail.com, address3@mail.com, address4@mail.com</p>
        <a href="?r=site%2Fgroups">Prehľad skupín</a>
        <h3>Úspešnosť</h3>
        <p>Newslettere si dnes otvorilo <strong>725</strong> odberateľov.</p>
      </div>
    </div>
    <?php } ?>
</div>