<?php

/* @var $this yii\web\View */
use yii\widgets\ActiveForm;
use yii\helpers\Html;
$this->title = 'Prehľad skupiny | BP Mailer';
?>

<div class="site-index">

    <h1>Prehľad skupiny</h1>

    <div class="container">
    	<?php
            $formInput = ActiveForm::begin([
                'id' => 'subscriberFromInput-form',
                'options' => ['class' => 'col-md-4'],
                            ]) ?>

				<div class="form-group">
					<?= $formInput->field($fromInput, 'emailAddress') ?>
				</div>

				<div class="form-group">
					<?= Html::submitButton('Pridať', ['class' => 'btn btn-primary', 'name' => 'subscriberFromInput-button']) ?>
				</div>
        <?php ActiveForm::end() ?>

        <?php
            $formImport = ActiveForm::begin([
                'id' => 'subscribersFromFile-form',
                'options' => ['class' => 'col-md-4'],
                            ]) ?>

				<div class="form-group">
					<?= $formImport->field($fromFile, 'importedFile')->fileInput() ?>
				</div>

				<div class="form-group">
					<?= Html::submitButton('Pridať', ['class' => 'btn btn-primary', 'name' => 'subscribersFromFile-button']) ?>
				</div>
        <?php ActiveForm::end() ?>

        <?php
            $formExport = ActiveForm::begin([
                'id' => 'exportToFile-form',
                'options' => ['class' => 'col-md-4'],
                            ]) ?>

				<label class="control-label">Exportovať do súboru</label>
				<div class="form-group">
					<?= $formExport->field($exportToFile, 'exportToCSV')->radio() ?>
				</div>

				<div class="form-group">
					<?= $formExport->field($exportToFile, 'exportToXML')->radio() ?>
				</div>
				<div class="form-group">
					<?= Html::submitButton('Pridať', ['class' => 'btn btn-primary', 'name' => 'exportToFile-button']) ?>
				</div>
        <?php ActiveForm::end() ?>

    </div>
</div>
