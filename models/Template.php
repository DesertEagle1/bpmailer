<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Template extends ActiveRecord
{
 
    public static function tableName()
    {
        return '{{%templates}}';
    }

    public static function findById($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function getTemplatesWithIds(){
        $templates = Template::find()
            ->all();

        $result = array();
        foreach ($templates as $key => $value) {
            $result[$value['id']] = $value['template_name'];
        }

        return $result;
    }

    public static function getSourceCodes(){
        $templates = Template::find()
            ->all();

        $result = array();
        foreach ($templates as $key => $value) {
            $result[$value['id']] = $value['source_code'];
        }

        return $result;
    }

}

?>