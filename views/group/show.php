<?php

/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
$this->title = 'Prehľad skupiny | BP Mailer';
?>

<div class="site-index">

    <h1>Prehľad skupiny</h1>

    <div class="container">
    	<div class="row">
    	<?php
            $form = ActiveForm::begin([
                'id' => 'subscriberFromInput-form',
                'options' => ['class' => 'col-md-4'],
                            ]) ?>

				<div class="form-group">
					<?= $form->field($model, 'emailAddress') ?>
				</div>

				<div class="form-group">
					<?= Html::submitButton('Pridať', ['class' => 'btn btn-primary', 'name' => 'subscriberFromInput-button']) ?>
				</div>
        <?php ActiveForm::end() ?>
        </div>
        <div class="row">
        <?php
            $formImport = ActiveForm::begin([
                'id' => 'subscribersFromFile-form',
                'options' => ['class' => 'col-md-4'],
                            ]) ?>

				<div class="form-group">
					<?= $formImport->field($modelImport, 'importedFile')->fileInput() ?>
				</div>

				<div class="form-group">
					<?= Html::submitButton('Importovať', ['class' => 'btn btn-primary', 'name' => 'subscribersFromFile-button']) ?>
				</div>
        <?php ActiveForm::end() ?>

        <?php
            $formExport = ActiveForm::begin([
                'id' => 'exportToFile-form',
                'options' => ['class' => 'col-md-4'],
                            ]) ?>

				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<?= $formExport->field($modelExport, 'exportFileFormat')->inline()->radioList($items) ?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<?= Html::submitButton('Exportovať', ['class' => 'btn btn-primary', 'name' => 'exportToFile-button']) ?>
				</div>
        <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
