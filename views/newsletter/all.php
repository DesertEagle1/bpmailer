<?php

/* @var $this yii\web\View */
/* @var $model app\models\NewsletterForm */
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Newslettere | BP Mailer';
?>

<div class="site-index">

    <h1>Newslettere</h1>


    <div class="container">
      <p class="text-right"><a class="btn btn-primary" href="<?= Url::to(['newsletter/new']) ?>" role="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Vytvoriť nový newsletter</a></p>
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Názov newslettera</th>
            <th>Stav</th>
            <th>Vytvorený</th>
            <th>Odoslaný</th>
            <th>Úspešnosť odoslania</th>
            <th>Otvorenia</th>
            <th>Prekliky</th>
            <th>Úspešnosť</th>
            <th></th>
          </tr>
        </thead>

        <?php
          $count = 1;
          echo "<tbody>";
          foreach ($newsletters as $key => $value) {
            echo "<tr>";
            echo "<td>" . $count . "</td>";
            if ($value['status'] == 'Uložený'){
              echo '<td><a href="'. Url::to(['newsletter/edit/', 'id' => $value['id']]) . '">' . $value['subject'] . 
              ' <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a></td>';
            }
            else {
              echo '<td><a href="'. Url::to(['newsletter/show/', 'id' => $value['id']]) . '">' . $value['subject'] . "</a></td>";
            }            
            echo "<td>" . $value['status'] . "</td>";
            echo "<td>" . $value['created_at'] . "</td>";
            echo "<td>" . ($value['sent_at'] ? $value['sent_at'] : "N/A") . "</td>";
            echo "<td>" . $value['successRate'] . "</td>";
            echo "<td>" . ($value['open'] ? $value['open'] : "N/A") . "</td>";
            echo "<td>" . ($value['clicks'] ? $value['clicks'] : "N/A") . "</td>";
            echo "<td>" . "N/A" . "</td>";
            if ($value['status'] == 'Uložený'){
              echo '<td><a class="btn btn-primary" href="' . Url::to(['newsletter/send/', 'id' => $value['id']]) . '" role="button">Odoslať</a></td>';
            }
            else {
              echo '<td><a class="btn btn-default" href="' . Url::to(['newsletter/show/', 'id' => $value['id']]) . '" role="button">Otvoriť</a></td>';
            }
            echo "<tr>";
            $count++;
          }
          echo "</tbody> ";
        ?> 

      </table>
    </div>
</div>
