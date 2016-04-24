<?php

/* @var $this yii\web\View */
/* @var $model app\models\NewsletterForm */
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/*$model = new \app\models\GroupForm();*/
$this->title = 'Vytvorenie novej šablóny | BP Mailer';
?>

<div class="site-index">

    <h1>Vytvorenie novej šablóny</h1>


    <div class="container">
      <form>
        <div class="form-group">
          <label for="sender">Názov šablóny</label>
          <input type="text" class="form-control" id="sender" placeholder="Názov šablóny">
        </div>

        <script src="ckeditor/ckeditor.js"></script>

        <div class="form-group">
          <textarea name="ckeditor" id="ckeditor">
            Lorem ipsum dolor sit amet, id vel ornatus maluisset. Vidisse nusquam salutatus vis an, nam vidit voluptatibus id, eam porro ignota dolorum ea. Dolore hendrerit comprehensam vis in, solum ignota in nec. Efficiendi comprehensam ut eam. Cum te quidam quodsi viderer, mel in nibh delectus theophrastus.
          </textarea>
          <script>
            CKEDITOR.replace('ckeditor');
          </script>
        </div>

        <button type="submit" class="btn btn-default">Uložiť</button>

      </form>
    </div><!-- /.container -->
</div>
