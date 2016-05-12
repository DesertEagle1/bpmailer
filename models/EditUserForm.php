<?php

namespace app\models;

use Yii;
use yii\base\Model;

class EditUserForm extends Model
{
    public $admin;
    public $newsletterAccess;
    public $groupAccess;
    public $templateAccess;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['admin', 'newsletterAccess', 'groupAccess', 'templateAccess'], 'boolean'],

            [['newsletterAccess', 'groupAccess', 'templateAccess'], 'required',
                'requiredValue' => true, 
                'when' => function ($model) {
                    return $model->admin == true;
                },
                'whenClient' => "function (attribute, value) {
                    return $('#edituser-admin').prop('checked');
                }",
                'message'=>'Administrátor musí mať všetky práva.'] 
        ];
    }

    public function attributeLabels()
    {
        return [
            'admin' => 'Administrátor',
            'newsletterAccess' => 'Vytvorenie a odoslanie newslettera',
            'groupAccess' => 'Správa skupín',
            'templateAccess' => 'Správa šablón',
        ];
    }

}
