<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * NewUserForm is the model behind the new user form.
 */
class NewUserForm extends Model
{
    public $username;
    public $password;
    public $passwordRepeat;
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
            ['username', 'required', 'message'=>'Prihlasovacie meno nesmie byť prázdne.'],
            ['username', 'usernameIsUnique'],
            [['password', 'passwordRepeat'], 'required', 'message'=>'Heslo nesmie byť prázdne.'],
            [['password','passwordRepeat'], 'string', 'min'=>5, 'tooShort'=>'Heslo musí obsahovať aspoň 5 znakov.'],
            [['admin', 'newsletterAccess', 'groupAccess', 'templateAccess'], 'boolean'],
            ['passwordRepeat', 'compare', 'compareAttribute'=>'password', 'message'=>'Heslá sa musia zhodovať.'],
            [['newsletterAccess', 'groupAccess', 'templateAccess'], 'required',
                'requiredValue' => true, 
                'when' => function ($model) {
                    return $model->admin == true;
                },
                'whenClient' => "function (attribute, value) {
                    return $('#newuserform-admin').prop('checked');
                }",
                'message'=>'Administrátor musí mať všetky práva.']
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Prihlasovacie meno',
            'password' => 'Heslo',
            'passwordRepeat' => 'Zopakovať heslo',
            'admin' => 'Administrátor',
            'newsletterAccess' => 'Vytvorenie a odoslanie newslettera',
            'groupAccess' => 'Správa skupín',
            'templateAccess' => 'Správa šablón',
        ];
    }

    public function usernameIsUnique(){
        if (User::findByUsername($this->username)) {
            $this->addError('username', 'Používateľ so zadaným menom už existuje.');
        }
    }

}
