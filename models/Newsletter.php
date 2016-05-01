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

    public static function getAllNewsletters(){
        $newsletters = Newsletter::find()
            ->orderBy('created_at DESC')
            ->all();

        $result = array();
        foreach ($newsletters as $key => $value) {
            $result[$key] = array();
            $result[$key]['id'] = $value['id'];
            $result[$key]['subject'] = $value['subject'];
            $result[$key]['status'] = Status::findById($value['status'])->status_name;
            $result[$key]['created_at'] = $value['created_at'];
            $result[$key]['sent_at'] = $value['sent_at'];
        }

        return $result;
    }
}
