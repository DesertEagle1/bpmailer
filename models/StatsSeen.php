<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class StatsSeen extends ActiveRecord
{
 
    public static function tableName()
    {
        return '{{%stats_seen}}';
    }

    public function getStats(){
    	return $this->hasOne(Stats::className(), ['id' => 'statistic_id']);
    }

    public static function countOpens($newsletterId){
    	$count = StatsSeen::find()
    			->joinWith('stats')
    			->where(['stats.newsletter_id' => $newsletterId])
    			->count();
    	return $count;
    }

}

?>