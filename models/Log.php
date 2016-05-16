<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Log extends ActiveRecord
{
 
    public static function tableName()
    {
        return '{{%logs}}';
    }

    public static function getLogs(){
        $logs = Log::find()
            ->innerJoinWith('activities')
            ->innerJoinWith('users')
            ->orderBy('created_at DESC');

        return $logs;
    }

    public static function writeLog($userId, $activityId, $info = null){
        $record = new Log();
        $record->user_id = $userId;
        $record->activity_id = $activityId;
        $record->additional_info = $info;
        $record->save();
    }

    public function getActivities()
    {
        return $this->hasOne(Activity::className(), ['id' => 'activity_id']);
    }

    public function getUsers()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

}

?>