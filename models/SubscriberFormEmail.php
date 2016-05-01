<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * NewGroupForm is the model behind the new group form.
 */
class SubscriberFormEmail extends Model
{
    public $emailAddress;
    /*public $importedFile;
    public $exportFileFormat;*/

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['emailAddress', 'required', 'message'=>'E-mail nesmie byť prázdny.'],
            ['emailAddress', 'email', 'message' => 'Zadajte platnú e-mailovú adresu'],
            //['exportFileFormat', 'required', 'message'=>'Musíte zvoliť formát súboru.']
        ];
    }

    public function attributeLabels()
    {
        return [
            'emailAddress' => 'Pridať nový e-mail',
            //'importedFile' => 'Importovať adresy zo súboru',
            //'exportFileFormat' => 'Exportovať do súboru'
        ];
    }

}
