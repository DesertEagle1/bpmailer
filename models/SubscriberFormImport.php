<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SubscriberFormImport extends Model
{
    public $importedFile;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [[['importedFile'], 'required', 'message' => 'Musíte vybrať nejaký súbor.'],
                [['importedFile'], 'file', 'extensions' => 'csv, xml', 'message' => 'Povolené iba súbory s príponou .csv a .xml'],
                [['importedFile'], 'file', 'maxSize' => 1024*1024*5, 'message' => 'Max. veľkosť súboru je 5MB.']

        ];
    }

    public function attributeLabels()
    {
        return [
            'importedFile' => 'Importovať adresy zo súboru',
        ];
    }

}
