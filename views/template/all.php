<?php

/* @var $this yii\web\View */
/* @var $model app\models\NewsletterForm */
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use dosamigos\ckeditor\CKEditor;

$this->title = 'Prehľad šablón | BP Mailer';
?>

<div class="site-index">

    <h1>Prehľad šablón</h1>

    <div class="container">
		<table class="table table-striped table-hover">
			<thead>
			  <tr>
			    <th>#</th>
			    <th>Názov šablóny</th>
			    <th></th>
			  </tr>
			</thead>

			<?php
			  $count = 1;
			  echo "<tbody>";
			  foreach ($templates as $key => $value) {
			    echo "<tr>";
			    echo "<td>" . $count . "</td>";
			    echo "<td>" . $value['template_name'] . "</td>";
			    echo '<td><a class="btn btn-default" href="' . Url::to(['template/show/', 'id' => $value['id']]) . '" role="button">Zobraziť šablónu</a></td>';
			    echo "<tr>";
			    $count++;
			  }
			  echo "</tbody> ";
			?>
		</table>
      
    </div>
</div>
