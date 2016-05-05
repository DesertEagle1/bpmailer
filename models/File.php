<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class File extends ActiveRecord
{
 
    public static function tableName()
    {
        return '{{%files}}';
    }

    public static function findById($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function findByHash($filenameHash)
    {
        return static::findOne(['filename_hash' => $filenameHash]);
    }

    public static function findByNewsletterId($newsletterId)
    {
        return static::findAll(['newsletter_id' => $newsletterId]);
    }

}

?>