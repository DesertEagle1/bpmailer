<?php

/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
$this->title = 'Prehľad skupiny | BP Mailer';
?>

<div class="site-index">

    <h1>Prehľad skupiny</h1>

    <div class="container">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><?= $groupInfo['group_name'] ?></h3>
          </div>
          <div class="panel-body">
            <?= $groupInfo['description'] ?>
          </div>
        </div>
        
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
                'options' => ['enctype' => 'multipart/form-data',
                                'class' => 'col-md-4']
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

        <h3>Zoznam adries</h3>
        <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>E-mailová adresa</th>
            <th></th>
          </tr>
        </thead>
        <?php
          echo "<tbody>";
          $count = 1;
          foreach ($addresses as $key => $value) {
            echo "<tr>";
            echo "<td>" . $count . "</td>";
            echo "<td>" . $value . "</td>";
            echo '<td><a class="btn btn-danger" href="#" role="button">Odstrániť</a></td>';
            echo "<tr>";
            $count++;
          }
          echo "</tbody> ";
        ?>
        </table>
    </div>
</div>
