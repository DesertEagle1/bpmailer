<?php

namespace app\models;

use Yii;
use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $oldPassword;
    public $newPassword;
    public $newPasswordRepeat;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['oldPassword','newPassword', 'newPasswordRepeat'], 'required', 'message'=>'{attribute} nesmie byť prázdne.'],
            ['newPasswordRepeat', 'compare', 'compareAttribute'=>'newPassword', 'message'=>'Heslá sa musia zhodovať.'],
            [['newPassword','newPasswordRepeat'], 'string', 'min'=>5, 'tooShort'=>'Heslo musí obsahovať aspoň 5 znakov.'],
            ['oldPassword','validatePassword', 'message'=>'Nesprávne heslo.']
        ];
    }

    public function attributeLabels()
    {
        return [
            'oldPassword' => 'Staré heslo',
            'newPassword' => 'Nové heslo',
            'newPasswordRepeat' => 'Nové heslo (zopakovať)'
        ];
    }

    public function validatePassword(){
        $user = User::findIdentity(Yii::$app->user->identity->id);
        
        if (!$user || !$user->validatePassword($this->oldPassword)) {
            return false;
        }
        return true;
    }

}
