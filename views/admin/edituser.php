<?php

/* @var $this yii\web\View */
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Správa prístupových práv | BP Mailer';
?>

<div class="site-index">

    <h1>Správa prístupových práv</h1>


    <div class="container">

        <h3><?= $username ?></h3>
        <?php $form = ActiveForm::begin([   
            'id' => 'edituser-form',
            'options' => ['class' => 'col-md-6'],
        ]); ?>
  
            <?= $form->field($model, 'admin')->checkbox() ?>

            <?= $form->field($model, 'newsletterAccess')->checkbox() ?>

            <?= $form->field($model, 'groupAccess')->checkbox() ?>

            <?= $form->field($model, 'templateAccess')->checkbox() ?>

            <div class="form-group">
                <div class="col-lg-offset-1 col-lg-11">
                    <?= Html::submitButton('Upraviť', ['class' => 'btn btn-primary', 'name' => 'edituser-button']) ?>
                </div>
            </div>

        <?php ActiveForm::end(); ?>

        <?php
        /*echo "<pre>";
        print_r($accessRights);
        print_r($model);
        echo "</pre>";*/
        ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#edituser-admin").click(function(){
                    if ($('#edituser-admin').is(':checked')) {
                        $("#edituser-newsletteraccess").prop('checked', true);
                        $("#edituser-groupaccess").prop('checked', true);
                        $("#edituser-templateaccess").prop('checked', true);
                }
                });
            });
        </script>
    </div>
</div>
