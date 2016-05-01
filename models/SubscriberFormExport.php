<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * NewGroupForm is the model behind the new group form.
 */
class SubscriberFormExport extends Model
{
    public $exportFileFormat;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['exportFileFormat', 'required', 'message'=>'Musíte zvoliť formát súboru.']
        ];
    }

    public function attributeLabels()
    {
        return [
            'exportFileFormat' => 'Exportovať do súboru'
        ];
    }

}
