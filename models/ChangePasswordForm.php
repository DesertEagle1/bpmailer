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
            ['newPassword', 'compare', 'compareAttribute'=>'newPasswordRepeat', 'message'=>'Heslá sa musia zhodovať.'],
            [['newPassword','newPasswordRepeat'], 'string', 'min'=>5, 'tooShort'=>'Heslo musí obsahovať aspoň 5 znakov.'],
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

}
