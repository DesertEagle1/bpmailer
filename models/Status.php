<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Status extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%statuses}}';
    }

    /**
     * @inheritdoc
     */
    public static function findById($id)
    {
        return static::findOne(['id' => $id]);
    }
}
