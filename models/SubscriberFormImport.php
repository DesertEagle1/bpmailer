<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * NewGroupForm is the model behind the new group form.
 */
class SubscriberFormImport extends Model
{
    public $importedFile;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [];
    }

    public function attributeLabels()
    {
        return [
            'importedFile' => 'Importovať adresy zo súboru',
        ];
    }

}
