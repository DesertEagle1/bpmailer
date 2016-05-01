<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadFiles extends Model
{
    /**
     * @var UploadedFile
     */
    public $attachments;

    public function rules()
    {
        return [
            [['attachments'], 'file', 'maxFiles' => 5, 'message' => 'Môžete nahrať max. 5 súborov.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'attachments' => 'Prílohy'
        ];
    }
    
    public function upload($currentID)
    {
        if ($this->validate()) { 
            foreach ($this->attachments as $file) {
                $saveFilename = new File();
                $saveFilename->filename = $file->baseName . '.' . $file->extension;
                $filenameHash = Yii::$app->getSecurity()->generateRandomString();
                while (File::findByHash($filenameHash)) {
                    $filenameHash = Yii::$app->getSecurity()->generateRandomString();
                }
                $saveFilename->filename_hash = $filenameHash . '.' . $file->extension;
                $saveFilename->newsletter_id = $currentID;
                $saveFilename->save();
                $file->saveAs('files/' . $filenameHash . '.' . $file->extension);
            }
            return true;
        } else {
            return false;
        }
    }
}