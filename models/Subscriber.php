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
}
