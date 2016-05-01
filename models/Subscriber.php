<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Subscriber extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%groups_emails}}';
    }

    /**
     * @inheritdoc
     */
    public static function findById($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function countSubscribers($groupId){
        $count = Subscriber::find()
                ->where(['group_id' => $groupId])
                ->count();
        return $count;
    }

    public static function emailInGroup($groupId, $email){
        $emailRecord = SubscriberEmail::findByEmail($email);

        $record = Subscriber::find()
                ->where(['group_id' => $groupId, 'email_id' => $emailRecord->id])
                ->one();

        if ($record != null){
            return false;
        }
        return true;
    }
}
