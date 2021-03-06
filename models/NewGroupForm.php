<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * NewGroupForm is the model behind the new group form.
 */
class NewGroupForm extends Model
{
    public $name;
    public $description;
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name','description'], 'required', 'message'=>'{attribute} nesmie byť prázdny.'],
            ['name', 'nameUnique']
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Názov skupiny',
            'description' => 'Popis skupiny',
            'file' => 'Súbor s adresami'
        ];
    }

    public function nameUnique(){
        if (Group::findGroupByName($this->name)) {
            $this->addError('name', 'Skupina so zadaným menom už existuje.');
        }
    }

}
