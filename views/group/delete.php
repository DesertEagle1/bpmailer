<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
$this->title = 'Vymazanie odberateľa | BP Mailer';
?>

<div class="site-index">

    <h1>Vymazanie odberateľa</h1>

    <div class="container">
    <p>Naozaj chcete vymazať používateľa <?= $subscriber ?> zo skupiny?</p>

      <?php
        $form = ActiveForm::begin([
            'id' => 'deletesubscriber-form',
            'options' => []
        ]) ?>

        <div class="form-group">
          <?= $form->field($model, 'hiddenInput')->hiddenInput()->label(false) ?>
        </div>  

        <div class="form-group">
            <?= Html::submitButton('Vymazať', ['class' => 'btn btn-danger', 'name' => 'deletesubscriber-button']) ?>
        </div>
        <?php ActiveForm::end() ?>

    </div>
</div>
