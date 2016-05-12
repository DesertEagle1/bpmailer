<?php

namespace app\models;

use Yii;
use yii\base\Model;

class EditTemplateForm extends Model
{
    public $sourceCode;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['sourceCode', 'required', 'message' => '{attribute} nesmie byť prázdny.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'sourceCode' => 'Zdrojový kód',
        ];
    }

}
