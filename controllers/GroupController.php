<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\data\Pagination;
use yii\helpers\Url;
use app\models\User;
use app\models\Group;
use app\models\AccessRights;
use app\models\NewGroupForm;
use app\models\Subscriber;
use app\models\SubscriberEmail;
use app\models\SubscriberFormEmail;
use app\models\SubscriberFormImport;
use app\models\SubscriberFormExport;
use app\models\DeleteSubscriberForm;
use app\models\Log;

class GroupController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionNew()
    {
        $rights = AccessRights::getAccessRightsForMenu(Yii::$app->user->id);
        Yii::$app->view->params['accessRightsArray'] = $rights;

        if (!empty(array_intersect([1,3], $rights))) {
            $model = new NewGroupForm();
            if ($model->load(Yii::$app->request->post()) && $model->validate()){
                $group = new Group();
                $group->group_name = $model->name;
                $group->description = $model->description;
                $group->save();

                Log::writeLog(Yii::$app->user->id, 4, $model->name);

                $id = $group->id;

                $file = UploadedFile::getInstance($model, 'file');
                if ($file === null){
                    Yii::$app->session->setFlash('success', 'Skupina ' . $model->name .' bola úspešne vytvorená.');
                    return $this->redirect(Url::to(['group/show/', 'id' => $id]));
                }
                $filename = 'data.'. $file->extension;
                $upload = $file->saveAs('uploads/'. $filename);

                if ($upload) {
                    Log::writeLog(Yii::$app->user->id, 5, $model->name);

                    // CSV súbor
                    if (strtolower($file->extension) == 'csv'){
                    define('CSV_PATH','uploads/');
                    $csv_file = CSV_PATH . $filename;
                    $filecsv = file($csv_file);

                    $this->importFromCSV($filecsv, $id);

                    unlink('uploads/'.$filename);
                    }

                else {
                    // XML súbor
                    define('XML_PATH','uploads/');
                    $xml_file = XML_PATH . $filename;
                    $filexml = file($xml_file);
                    $data = implode("", $filexml);
                    $parser = xml_parser_create();
                    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
                    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
                    xml_parse_into_struct($parser, $data, $values, $tags);
                    xml_parser_free($parser);

                    $this->importFromXML($values, $id);

                    unlink('uploads/'.$filename);
                    
                    }
                    Yii::$app->session->setFlash('success', 'Skupina ' . $model->name .' bola úspešne vytvorený.');
                    return $this->redirect(Url::to(['group/show/', 'id' => $id]));
                }
            }
            return $this->render('new', array('model' => $model));
        }

        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

    public function actionAll()
    {
        $rights = AccessRights::getAccessRightsForMenu(Yii::$app->user->id);
        Yii::$app->view->params['accessRightsArray'] = $rights;

        if (!empty(array_intersect([1,3], $rights))) {
            $groups = Group::getAllGroups();
            return $this->render('all', array('groups' => $groups));
        }

        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

    public function actionShow($id, $page = 0)
    {
        $rights = AccessRights::getAccessRightsForMenu(Yii::$app->user->id);
        Yii::$app->view->params['accessRightsArray'] = $rights;

        if (!empty(array_intersect([1,3], $rights))) {
            $model = new SubscriberFormEmail();
            $groupInfo = Group::findGroupById($id);

            $query = Subscriber::find()
                        ->innerJoinWith('emails')
                        ->where(['group_id' => $id]);
            $count = $query->count();
            $pagination = new Pagination(['totalCount' => $count]);
            $addresses = $query->offset($pagination->offset)
                        ->limit($pagination->limit)
                        ->all();

            $items = array('csv'=>'.CSV', 'xml'=>'.XML');

            if ($groupInfo == null){
                return $this->render('error', array('name'=>'Skupina neexistuje', 
                                    'message'=>'Skupina so zadaným ID neexistuje'));
            }

            // jeden e-mail zo vstupu
            if ($model->load(Yii::$app->request->post()) && $model->validate()){
                $subscriber = new SubscriberEmail();
                if (!(SubscriberEmail::findByEmail($model->emailAddress))){
                    $subscriber->email = $model->emailAddress;
                    $subscriber->save();
                }
                else {
                    Yii::$app->session->setFlash('error', 'Adresa sa už nachádza v skupine.');
                }

                if (Subscriber::emailInGroup($id, $model->emailAddress)){
                    $emailId = SubscriberEmail::findByEmail($model->emailAddress);
                    $subscriber = new Subscriber();
                    $subscriber->group_id = $id;
                    $subscriber->email_id = $emailId->id;
                    $randomString = Yii::$app->getSecurity()->generateRandomString();
                    while (Subscriber::find()->where(['token' => $randomString])->one()) {
                        $randomString = Yii::$app->getSecurity()->generateRandomString();
                    }
                    $subscriber->token = $randomString;
                    $subscriber->save();

                    Log::writeLog(Yii::$app->user->id, 6, ($groupInfo->group_name . ',' . $model->emailAddress));

                    Yii::$app->session->setFlash('success', 'Adresa bola úspešne pridaná do skupiny.');
                }

                return $this->refresh();
            }

            // e-maily z importu
            $modelImport = new SubscriberFormImport();
            if ($modelImport->load(Yii::$app->request->post())){
                $file = UploadedFile::getInstance($modelImport, 'importedFile');
                $filename = 'data.'. $file->extension;
                $upload = $file->saveAs('uploads/'. $filename);

                if ($upload) {
                    Log::writeLog(Yii::$app->user->id, 5, $groupInfo->group_name);
                    // CSV súbor
                    if (strtolower($file->extension) == 'csv'){
                    define('CSV_PATH','uploads/');
                    $csv_file = CSV_PATH . $filename;
                    $filecsv = file($csv_file);

                    $this->importFromCSV($filecsv, $id);

                    unlink('uploads/'.$filename);
                    Yii::$app->session->setFlash('success', 'Import adries prebehol úspešne.');
                    return $this->refresh();
                    }

                    // XML súbor
                    else {

                    define('XML_PATH','uploads/');
                    $xml_file = XML_PATH . $filename;
                    $filexml = file($xml_file);
                    $data = implode("", $filexml);
                    $parser = xml_parser_create();
                    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
                    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
                    xml_parse_into_struct($parser, $data, $values, $tags);
                    xml_parser_free($parser);

                    $this->importFromXML($values, $id);

                    unlink('uploads/'.$filename);
                    Yii::$app->session->setFlash('success', 'Import adries prebehol úspešne.');
                    return $this->refresh();
                    }
                }
            }

            $modelExport = new SubscriberFormExport();
            if ($modelExport->load(Yii::$app->request->post())){
                $extension = $modelExport->exportFileFormat;
                $data = Subscriber::getAddressesFromGroup($id);
                if ($extension == 'csv'){
                    return $this->renderPartial('exportCSV', array('data' => $data));
                }

                if ($extension == 'xml') {
                    return $this->renderPartial('exportXML', array('data' => $data));
                }
            }

            return $this->render('show', 
                array('model'=>$model, 'modelImport'=>$modelImport, 'modelExport'=>$modelExport, 'items'=>$items,
                        'addresses' => $addresses, 'groupInfo' => $groupInfo, 'pagination' => $pagination));
        }

        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

    public function importFromCSV($filecsv, $id){
        foreach ($filecsv as $data) {
            $line = explode(",", $data);
            foreach ($line as $key => $value) {
                $value = trim($value);
                if(filter_var($value, FILTER_VALIDATE_EMAIL)){
                    if (Subscriber::emailInGroup($id, $value)){
                        if (SubscriberEmail::findByEmail($value) === null){
                            $newEmail = new SubscriberEmail();
                            $newEmail->email = $value;
                            $newEmail->save();
                            $emailId = $newEmail->id;
                        }
                        else {
                            $emailId = SubscriberEmail::findByEmail($value)->id;
                        }

                        $newEmailInGroup = new Subscriber();
                        $newEmailInGroup->group_id = $id;
                        $newEmailInGroup->email_id = $emailId;
                        $randomString = Yii::$app->getSecurity()->generateRandomString();
                        while (Subscriber::find()->where(['token' => $randomString])->one()) {
                            $randomString = Yii::$app->getSecurity()->generateRandomString();
                        }
                        $newEmailInGroup->token = $randomString;
                        $newEmailInGroup->save();
                    }
                }
            } 
        }
    }

    public function importFromXML($values, $id){
        foreach ($values as $key => $value) {
            if (strtolower($value['tag']) == "email" or strtolower($value['tag']) == 'e-mail') {
                $address = trim($value['value']);
                if(filter_var($address, FILTER_VALIDATE_EMAIL)){
                    if (Subscriber::emailInGroup($id, $address)){
                        if (SubscriberEmail::findByEmail($address) === null){
                            $newEmail = new SubscriberEmail();
                            $newEmail->email = $address;
                            $newEmail->save();
                            $emailId = $newEmail->id;
                        }
                        else {
                            $emailId = SubscriberEmail::findByEmail($address)->id;
                        }

                        $newEmailInGroup = new Subscriber();
                        $newEmailInGroup->group_id = $id;
                        $newEmailInGroup->email_id = $emailId;
                        $randomString = Yii::$app->getSecurity()->generateRandomString();
                        while (Subscriber::find()->where(['token' => $randomString])->one()) {
                            $randomString = Yii::$app->getSecurity()->generateRandomString();
                        }
                        $newEmailInGroup->token = $randomString;
                        $newEmailInGroup->save();
                    }
                }
            }
        }
    }

    public function actionDelete($groupid, $emailid)
    {
        $rights = AccessRights::getAccessRightsForMenu(Yii::$app->user->id);
        Yii::$app->view->params['accessRightsArray'] = $rights;

        if (!empty(array_intersect([1,3], $rights))) {
            $model = new DeleteSubscriberForm();

            $subscriber = SubscriberEmail::findById($emailid);
            if ($subscriber === null){
                return $this->render('error', array('name'=>'Používateľ neexistuje', 
                                                    'message'=>'Odberateľ so zadaným ID neexistuje.'));
            }
            $group = Group::findGroupById($groupid);
            if ($group === null){
                return $this->render('error', array('name'=>'Skupina neexistuje', 
                                                    'message'=>'Skupina so zadaným ID neexistuje.'));
            }
            $member = Subscriber::findOne(['group_id' => $groupid, 'email_id' => $emailid]);
            if ($member === null){
                return $this->render('error', array('name'=>'Odberateľ nie je v skupine', 
                                                    'message'=>'Odberateľ so zadaným ID sa nenachádza v tejto skupine.'));
            }
            if ($model->load(Yii::$app->request->post())){
                $record = Subscriber::findOne(['group_id' => $groupid, 'email_id' => $emailid]);
                $record->delete();
                Log::writeLog(Yii::$app->user->id, 7, ($group->group_name . ',' . $subscriber->email));

                $exists = Subscriber::findOne(['email_id' => $emailid]);
                if ($exists === null){
                    $record = SubscriberEmail::findOne(['id' => $emailid]);
                    $record->delete();
                }

                return $this->redirect(Url::to(['group/show/', 'id' => $groupid]));
            }
            return $this->render('delete', array('model' => $model, 'subscriber' => $subscriber->email));
        }

        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }
}
