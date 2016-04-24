<?php

namespace app\models;

use Yii;
use yii\base\Model;

class NewsletterForm extends Model
{
    public $subject;
    public $receivers;
    public $copyTo;
    public $sentFrom;
    public $replyTo;
    public $template;
    public $content;
    public $attachment;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['subject', 'sentFrom', 'content'], 'required', 'message' => '{attribute} nesmie byť prázdny.'],
            [['receivers'], 'required', 'message' => 'Príjemcovia nesmú byť prázdni.'],
            [['replyTo', 'sentFrom'], 'email', 'message' => 'Zadajte platnú e-mailovú adresu.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'subject' => 'Predmet',
            'receivers' => 'Príjemcovia',
            'copyTo' => 'Kópia',
            'sentFrom' => 'Odosielateľ',
            'replyTo' => 'Odpovedať na',
            'template' => 'Šablóna',
            'content' => 'Telo správy',
            'attachment' => 'Príloha',
        ];
    }
}
