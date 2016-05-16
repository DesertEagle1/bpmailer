<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use scotthuangzl\googlechart\GoogleChart;

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
        <div class="col-lg-6">
        <?php  
        echo GoogleChart::widget(array('visualization' => 'PieChart',
                'data' => array(
                    array('Task', 'Hours per Day'),
                    array('Work', 11),
                    array('Eat', 2),
                    array('Commute', 2),
                    array('Watch TV', 2),
                    array('Sleep', 7)
                ),
                'options' => array(
                    'title' => 'My Daily Activity',
                    )));
        ?>
        </div>

        <div class="col-lg-6">
        <?php
        echo GoogleChart::widget(array('visualization' => 'LineChart',
                'data' => array(
                    array('Task', 'Hours per Day'),
                    array('Work', 11),
                    array('Eat', 2),
                    array('Commute', 2),
                    array('Watch TV', 2),
                    array('Sleep', 7)
                ),
                'options' => array(
                    'title' => 'My Daily Activity',
                    'legend' => array('position' => 'bottom'),
                )));
        ?>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-6">
        <h2>Najnovšie newslettere</h2>
        <p class="text-right"><a class="btn btn-primary" href="<?= Url::to(['newsletter/new']) ?>" role="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Vytvoriť nový newsletter</a></p>
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
            <?php
              foreach ($newsletters as $key => $value) {
                echo "<tr>";
                $date = new DateTime($value['created_at']);
                $date = $date->format('d.m.Y');
                echo '<td>' . $date . '</td>';
                echo '<td>' . $value['subject'] . '</td>';
                echo '<td>' . $value['subscribersCount'] . '</td>';
                echo '<td>N/A</td>';
                echo "</tr>";

              }
            ?>
          </tbody>  

        </table>
        <p><a href="<?= Url::to(['newsletter/all']) ?>">Zoznam všetkých newsletterov</a></p>
      </div>
      <div class="col-lg-6">
        <h2>Dnešná aktivita (<?= date('d.n.Y') ?>)</h2>
        
        <h3>Noví odberatelia</h3>
        <p>Dnes pribudlo <strong> <?= sizeof($todaySubscribers) ?></strong> nových odberateľov:
        <?php
          foreach ($todaySubscribers as $key => $value) {
            echo $value . ', ';
          }
        ?> </p>
        <a href="<?= Url::to(['group/all']) ?>">Prehľad skupín</a>
        <h3>Úspešnosť</h3>
        <p>Newslettere si dnes otvorilo <strong>725</strong> odberateľov.</p>
      </div>
    </div>
    <?php } ?>
</div>