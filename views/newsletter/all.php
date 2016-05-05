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
      <p class="text-right"><a class="btn btn-primary" href="?r=newsletter%2Fnew" role="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Vytvoriť nový newsletter</a></p>
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
            echo '<td><a href="?r=newsletter%2Fshow&id='. $value['id'] . '">' . $value['subject'] . "</a></td>";
            echo "<td>" . $value['status'] . "</td>";
            echo "<td>" . $value['created_at'] . "</td>";
            echo "<td>" . ($value['sent_at'] ? $value['sent_at'] : "N/A") . "</td>";
            echo "<td>" . "N/A" . "</td>";
            echo "<td>" . "N/A" . "</td>";
            echo "<td>" . "N/A" . "</td>";
            echo "<td>" . "N/A" . "</td>";
            if ($value['status'] == 'Uložený'){
              echo '<td><a class="btn btn-primary" href="?r=newsletter%2Fsend&id=' . $value['id'] . '" role="button">Odoslať</a></td>';
            }
            else {
              echo '<td><a class="btn btn-default" href="?r=newsletter%2Fshow&id=' . $value['id'] . '" role="button">Otvoriť</a></td>';
            }
            echo "<tr>";
            $count++;
          }
          echo "</tbody> ";
        ?> 

      </table>
    </div>
</div>
