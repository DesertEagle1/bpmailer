<?php

namespace app\models;

use Yii;
use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $oldPassword;
    public $newPassword;
    public $newPasswordRepeat;

    private $_user;

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

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->oldPassword)) {
                $this->addError($attribute, 'Nesprávne heslo');
            }
        }
    }

    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findIdentity(Yii::$app->user->identity->id);
        }

        return $this->_user;
    }

}
