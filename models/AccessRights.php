<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class AccessRights extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%users_access_rights}}';
    }

    public static function getAccessRights($userID)
    {
        $rights = AccessRights::find()
            ->select(['access_right_id'])
            ->where(['user_id' => $userID])
            ->all();

        return $rights;
    }

    public static function getAccessRightsForMenu($userID)
    {
        $rights = AccessRights::find()
            ->select(['access_right_id'])
            ->where(['user_id' => $userID])
            ->all();

        $result = array();
        foreach ($rights as $key => $value) {
             $result[] = $rights[$key]['access_right_id'];
         } 

        return $result;
    }

    public static function getAllAccessRights()
    {
        $users = User::find()
            ->all();

        $userRights = array();
        foreach ($users as $key => $value) {
            $userRights[$value['username']]  = array();
        }

        $rights = AccessRights::find()
            ->all();

        foreach ($rights as $key => $value) {
            $username = User::findIdentity($rights[$key]['user_id'])->username;
            $userRights[$username][] = $rights[$key]['access_right_id'];
        }

        return $userRights;
    }
 
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public static function createNewAccessRight($user_id, $access_right_id){
        $right = new AccessRights();
        $right->user_id = $user_id;
        $right->access_right_id = $access_right_id;
        $right->save();
    }

    public static function deleteAccessRight($user_id, $access_right_id){
        $record = AccessRights::findOne([
                'user_id' => $user_id, 
                'access_right_id' => $access_right_id
                ]);
        if ($record) {
            $record->delete();
        }
    }

}
