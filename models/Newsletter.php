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
}
