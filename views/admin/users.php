<?php

/* @var $this yii\web\View */
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;

$this->title = 'Správa prístupových práv | BP Mailer';
?>

<div class="site-index">

    <h1>Správa prístupových práv</h1>

    <?php
     if (Yii::$app->session->hasFlash('success')){
        echo '<div class="alert alert-success" role="alert">';
        echo Yii::$app->session->getFlash('success');
        echo "</div>";
     }
    ?>

    <div class="container">

    <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Používateľ</th>
            <th class="text-center">Administrátor</th>
            <th class="text-center">Vytvorenie a odoslanie newslettera</th>
            <th class="text-center">Správa skupín</th>
            <th class="text-center">Správa šablón</th>
            <th></th>
          </tr>
        </thead>

        <tbody>
            <?php
            $count = 1;
            foreach ($allRights as $key => $value) {
                if (Yii::$app->user->id == User::findByUsername($key)->id) {
                    continue;
                }
                echo "<tr>";
                echo "<td>" . $count . "</td>";
                echo "<td>" . $key . "</td>";
                echo '<td class="text-center"><span class="glyphicon ' . (in_array(1, $value) ? 'glyphicon-ok ' : ' ') . 'aria-hidden="true"></span></td>';
                echo '<td class="text-center"><span class="glyphicon ' . (in_array(2, $value) ? 'glyphicon-ok ' : ' ') . 'aria-hidden="true"></span></td>';
                echo '<td class="text-center"><span class="glyphicon ' . (in_array(3, $value) ? 'glyphicon-ok ' : ' ') . 'aria-hidden="true"></span></td>';
                echo '<td class="text-center"><span class="glyphicon ' . (in_array(4, $value) ? 'glyphicon-ok ' : ' ') . 'aria-hidden="true"></span></td>';
                echo '<td class="text-center"><a class="btn btn-primary" href="'. Url::to(['admin/edituser', 'id' => $usersIdList[$key]]) . '" role="button">Upraviť</a></td>';
                echo "</tr>";
                $count++;
            }
            ?>
        </tbody>

    </table>
    <p><a class="btn btn-primary" href="<?= Url::to(['admin/newuser']) ?>" role="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Pridať nového používateľa</a></p>
    </div>
</div>
