<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Newsletter extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%newsletters}}';
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public static function findById($id){
        return static::findOne(['id' => $id]);
    }
    

    public static function getAllNewsletters(){
        $newsletters = Newsletter::find()
            ->orderBy('created_at DESC')
            ->joinWith('stats')
            ->all();

        $result = array();
        foreach ($newsletters as $key => $value) {
            $result[$key] = array();
            $result[$key]['id'] = $value['id'];
            $result[$key]['subject'] = $value['subject'];
            $result[$key]['status'] = Status::findById($value['status'])->status_name;
            if ($value['copy_to']) {
                $numberofCopies = sizeof(explode(",", $value['copy_to']));
            }
            else {
                $numberofCopies = 0;
            }          
            $result[$key]['subscribersCount'] = Subscriber::countSubscribers($value['send_to_group']) + $numberofCopies;
            $result[$key]['created_at'] = $value['created_at'];
            $result[$key]['sent_at'] = $value['sent_at'];
            if ($value['sent_at'] === null){
                $rate = 'N/A';
            }
            else {
                $rate = explode('/', $value['stats']['receivers']);
                $rate = round($rate[0] / $rate[1], 1) * 100 . '%';
            }
                
            $result[$key]['successRate'] = $rate;
            $result[$key]['open'] = StatsSeen::countOpens($value['id']);
            $result[$key]['clicks'] = StatsClicks::countClicks($value['id']);

        }

        return $result;
    }


    public static function getNewslettersBetweenDates($from, $to){
        $result = Newsletter::find()
            ->where(['between', 'sent_at', $from, $to])
            ->count();

        return $result;
    }

    public function getStats()
    {
        return $this->hasOne(Stats::className(), ['newsletter_id' => 'id']);
    }
}
