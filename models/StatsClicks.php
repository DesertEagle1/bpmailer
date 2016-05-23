<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class StatsClicks extends ActiveRecord
{
 
    public static function tableName()
    {
        return '{{%stats_clicks}}';
    }

    public function getStats(){
    	return $this->hasOne(Stats::className(), ['id' => 'statistic_id']);
    }

    public static function countClicks($newsletterId){
    	$count = StatsClicks::find()
    			->joinWith('stats')
    			->where(['stats.newsletter_id' => $newsletterId])
    			->count();
    	return $count;
    }

}

?>