<?php

/* @var $this yii\web\View */
/* @var $model app\models\NewsletterForm */
use yii\widgets\ActiveForm;
use yii\helpers\Html;
$this->title = 'Prehľad skupín | BP Mailer';
?>

<div class="site-index">

    <h1>Prehľad skupín</h1>


    <div class="container">
      
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Názov skupiny</th>
            <th>Popis skupiny</th>
            <th>Počet adries</th>
            <th></th>
          </tr>
        </thead>
   
        <?php
          $count = 1;
          echo "<tbody>";
          foreach ($groups as $key => $value) {
            echo "<tr>";
            echo "<td>" . $count . "</td>";
            echo "<td>" . $value['name'] . "</td>";
            echo "<td>" . $value['description'] . "</td>";
            echo "<td>" . $value['count'] . "</td>";
            echo '<td><a class="btn btn-default" href="?r=group%2Fshow&id=' . $value['id'] . '" role="button">Prehľad skupiny</a></td>';
            echo "<tr>";
            $count++;
          }
          echo "</tbody> ";
        ?>
      
      </table>
      <p class="text-left"><a class="btn btn-primary" href="?r=group%2Fnew" role="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Vytvoriť novú skupinu</a></p>
    </div>
</div>
