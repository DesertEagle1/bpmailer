<?php

/* @var $this yii\web\View */
/* @var $model app\models\NewsletterForm */
use yii\widgets\ActiveForm;
use yii\helpers\Html;
$this->title = 'Newslettere | BP Mailer';
?>

<div class="site-index">

    <h1>Newslettere</h1>


    <div class="container">
      <p class="text-right"><a class="btn btn-primary" href="?r=site%2Fnewsletter" role="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Vytvoriť nový newsletter</a></p>
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Názov newslettera</th>
            <th>Stav</th>
            <th>Vytvorená</th>
            <th>Odoslaná</th>
            <th>Úspešnosť odoslania</th>
            <th>Otvorenia</th>
            <th>Prekliky</th>
            <th>Úspešnosť</th>
            <th></th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <td><a href="newsletter.html">Výpredaj notebookov</a></td>
            <td>Uložená</td>
            <td>1.1.2016 12:34:56</td>
            <td>N/A</td>
            <td>N/A</td>
            <td>N/A</td>
            <td>N/A</td>
            <td>N/A</td>
            <td><button type="button" class="btn btn-primary">Odoslať</button</td>
          </tr>

          <tr>
            <td>Späť do školy</td>
            <td>Odoslaná</td>
            <td>1.1.2016 12:34:56</td>
            <td>1.1.2016 12:45:56</td>
            <td>1702/1709</td>
            <td>1021</td>
            <td>136</td>
            <td>59,9%</td>
            <td><button type="button" class="btn btn-success">Prehľad</button</td>
          </tr>

          <tr>
            <td>Výpredaj notebookov</td>
            <td>Odoslaná</td>
            <td>1.1.2016 12:34:56</td>
            <td>1.1.2016 12:45:56</td>
            <td>1702/1709</td>
            <td>1021</td>
            <td>136</td>
            <td>59,9%</td>
            <td><button type="button" class="btn btn-success">Prehľad</button</td>
          </tr>

        </tbody>  

      </table>

    </div><!-- /.container -->
</div>
