<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class SubscriberEmail extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%emails}}';
    }

    /**
     * @inheritdoc
     */
    public static function findById($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function findByEmail($email){
        return static::findOne(['email' => $email]);
    }
}
