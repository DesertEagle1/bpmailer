<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * NewTemplateForm is the model behind the new template form.
 */
class NewTemplateForm extends Model
{
    public $name;
    public $sourceCode;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'sourceCode'], 'required', 'message'=>'{attribute} nesmie byť prázdny.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Názov šablóny',
            'sourceCode' => 'Zdrojový kód',
        ];
    }

}
