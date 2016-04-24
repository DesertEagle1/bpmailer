<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * NewGroupForm is the model behind the new group form.
 */
class SubscriberForm extends Model
{
    public $emailAddress;
    public $importedFile;
    public $exportToCSV;
    public $exportToXML;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['emailAddress', 'required', 'message'=>'E-mail nesmie byť prázdny.'],
            ['emailAddress', 'email', 'message' => 'Zadajte platnú e-mailovú adresu'],
            [['exportToXML', 'exportToCSV'], 'boolean']
        ];
    }

    public function attributeLabels()
    {
        return [
            'emailAddress' => 'Pridať nový e-mail',
            'importedFile' => 'Importovať adresy zo súboru',
            'exportToCSV' => '.CSV',
            'exportToXML' => '.XML'
        ];
    }

}
