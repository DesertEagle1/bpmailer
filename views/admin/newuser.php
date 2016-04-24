<?php

/* @var $this yii\web\View */
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Vytvorenie nového používateľa | BP Mailer';
?>

<div class="site-index">

    <h1>Vytvorenie nového používateľa</h1>

    <div class="container">

        <?php $form = ActiveForm::begin([   
            'id' => 'newuser-form',
            'options' => ['class' => 'col-md-6'],
        ]); ?>
  
            <?= $form->field($model, 'username') ?>       
    
            <?= $form->field($model, 'password')->passwordInput() ?>
                    
            <?= $form->field($model, 'passwordRepeat')->passwordInput() ?>

            <?= $form->field($model, 'admin')->checkbox() ?>

            <?= $form->field($model, 'newsletterAccess')->checkbox() ?>

            <?= $form->field($model, 'groupAccess')->checkbox() ?>

            <?= $form->field($model, 'templateAccess')->checkbox() ?>

            <div class="form-group">
                <div class="col-lg-offset-1 col-lg-11">
                    <?= Html::submitButton('Vytvoriť', ['class' => 'btn btn-primary', 'name' => 'newuser-button']) ?>
                </div>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $("#newuserform-admin").click(function(){
            if ($('#newuserform-admin').is(':checked')) {
                $("#newuserform-newsletteraccess").prop('checked', true);
                $("#newuserform-groupaccess").prop('checked', true);
                $("#newuserform-templateaccess").prop('checked', true);
        }
        });
    });
</script>
