<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Group extends ActiveRecord
{
 
    public static function tableName()
    {
        return '{{%groups}}';
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function getAllGroups(){
    	$groups = Group::find()
            ->all();

        $result = array();
        foreach ($groups as $key => $value) {
        	$result[$key] = array();
        	$result[$key]['id'] = $value['id'];
        	$result[$key]['name'] = $value['group_name'];
        	$result[$key]['description'] = $value['description'];
        	$result[$key]['count'] = Subscriber::countSubscribers($value['id']);
        }

        return $result;
    }

    public static function getGroupsWithIds(){
        $groups = Group::find()
            ->all();

        $result = array();
        foreach ($groups as $key => $value) {
            $result[$value['id']] = $value['group_name'];
        }

        return $result;
    }


}

?>