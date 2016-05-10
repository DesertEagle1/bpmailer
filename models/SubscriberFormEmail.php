<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SubscriberFormEmail extends Model
{
    public $emailAddress;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['emailAddress', 'required', 'message'=>'E-mail nesmie byť prázdny.'],
            ['emailAddress', 'email', 'message' => 'Zadajte platnú e-mailovú adresu']
        ];
    }

    public function attributeLabels()
    {
        return [
            'emailAddress' => 'Pridať nový e-mail',
        ];
    }

}
