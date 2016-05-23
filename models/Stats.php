<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Stats extends ActiveRecord
{
 
    public static function tableName()
    {
        return '{{%stats}}';
    }

}

?>