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
        if ($emailRecord == null){
            return true;
        }

        $record = Subscriber::find()
                ->where(['group_id' => $groupId, 
                        'email_id' => $emailRecord->id])
                ->one();

        if ($record != null){
            return false;
        }
        return true;
    }

    public static function getAddressesFromGroup($groupId){
        $addresses = Subscriber::find()
            ->innerJoinWith('emails')
            ->where(['group_id' => $groupId])
            ->all();

        $result = array();
        foreach ($addresses as $key => $value) {
            $result[] = $value['emails']['email'];
        }

        return $result;
    }

    public static function getAddressesAndIds($groupId){
        $addresses = Subscriber::find()
            ->innerJoinWith('emails')
            ->where(['group_id' => $groupId])
            ->all();

        $result = array();
        foreach ($addresses as $key => $value) {
            $result[$value['emails']['email']] = array('group_id' => $value['group_id'], 'email_id' => $value['email_id']);
        }

        return $result;
    }

    public function getEmails()
    {
        return $this->hasOne(SubscriberEmail::className(), ['id' => 'email_id']);
    }
}
