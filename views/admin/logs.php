<?php

/* @var $this yii\web\View */
use yii\widgets\LinkPager;

$this->title = 'Logy | BP Mailer';
?>

<div class="site-index">

    <h1>Logy</h1>

    <?php
        echo LinkPager::widget([
            'pagination' => $pagination,
        ]);
    ?>
    
    <div class="container">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Používateľ</th>
            <th>Aktivita</th>
            <th>Dátum a čas</th>
          </tr>
        </thead>

        <tbody>

        <?php
        	function formatActivity($activityId, $info){
        		if ($activityId == 2){
        			return 'Vytvorenie newslettera <b>' . $info . '</b>';
        		}

        		if ($activityId == 3){
        			return 'Odoslanie newslettera <b>' . $info . '</b>';
        		}

        		if ($activityId == 4){
        			return 'Vytvorenie skupiny <b>' . $info . '</b>';
        		}

        		if ($activityId == 5){
        			return 'Import odberateľov do skupiny <b>' . $info . '</b>';
        		}

        		if ($activityId == 6){
        			$delimiter = strrpos($info, ",");
        			$group = substr($info, 0, $delimiter);
        			$email = substr($info, $delimiter+1, strlen($info));
        			return 'Pridaný odberateľ <b>' . $email . '</b> do skupiny <b>' . $group . '</b>';
        		}

        		if ($activityId == 7){
        			$delimiter = strrpos($info, ",");
        			$group = substr($info, 0, $delimiter);
        			$email = substr($info, $delimiter+1, strlen($info));
        			return 'Vymazaný odberateľ <b>' . $email . '</b> zo skupiny <b>' . $group . '</b>';
        		}

        		if ($activityId == 8){
        			return 'Upravený newsletter <b>' . $info . '</b>';
        		}

        		if ($activityId == 9){
        			return 'Nová šablóna <b>' . $info . '</b>';
        		}

        		if ($activityId == 10){
        			return 'Upravená šablóna <b>' . $info . '</b>';
        		}

        		if ($activityId == 11){
        			return 'Nový používateľ <b>' . $info . '</b>';
        		}

        		if ($activityId == 12){
        			return 'Úprava prístupových práv používateľa <b>' . $info . '</b>';
        		}

        	}

        	$count = 1;
            $request = Yii::$app->request;
            $pageNumber = $request->get('page', 1);
            if ($pageNumber == 0) {$pageNumber = 1;}
        	foreach ($logs as $key => $value) {
        		echo "<tr>";
        		echo '<td>' . ($count + 20*($pageNumber-1)) . '</td>';
        		echo '<td>' . $value['users']['username'] . '</td>';
        		echo '<td>' . formatActivity($value['activity_id'], $value['additional_info']) . '</td>';
        		echo '<td>' . $value['created_at'] . '</td>';
        		echo "</tr>";
                $count += 1;
        	}
        ?>	

        </tbody>
      </table>
    </div>
</div>
