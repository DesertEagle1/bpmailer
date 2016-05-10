<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\Group;
use app\models\AccessRights;
use app\models\NewGroupForm;
use app\models\Subscriber;
use app\models\SubscriberEmail;
use app\models\SubscriberFormEmail;
use app\models\SubscriberFormImport;
use app\models\SubscriberFormExport;
use yii\web\UploadedFile;

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
        $rights = AccessRights::getAccessRights(Yii::$app->user->id);
        $result = array();
        foreach ($rights as $key => $value) {
             $result[] = $rights[$key]['access_right_id'];
         } 
        Yii::$app->view->params['accessRightsArray'] = $result;

        if (!empty(array_intersect([1,3], $result))) {
            $model = new NewGroupForm();
            if ($model->load(Yii::$app->request->post()) && $model->validate()){
                $group = new Group();
                $group->group_name = $model->name;
                $group->description = $model->description;
                $group->save();
            }
            return $this->render('new', array('model' => $model));
        }

        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

    public function actionAll()
    {
        $rights = AccessRights::getAccessRights(Yii::$app->user->id);
        $result = array();
        foreach ($rights as $key => $value) {
             $result[] = $rights[$key]['access_right_id'];
         } 
        Yii::$app->view->params['accessRightsArray'] = $result;

        if (!empty(array_intersect([1,3], $result))) {
            $groups = Group::getAllGroups();
            return $this->render('all', array('groups' => $groups));
        }

        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

    public function actionShow($id)
    {
        $rights = AccessRights::getAccessRights(Yii::$app->user->id);
        $result = array();
        foreach ($rights as $key => $value) {
             $result[] = $rights[$key]['access_right_id'];
         } 
        Yii::$app->view->params['accessRightsArray'] = $result;

        if (!empty(array_intersect([1,3], $result))) {
            $model = new SubscriberFormEmail();
            $modelExport = new SubscriberFormExport();
            $groupInfo = Group::findGroupById($id);
            $addresses = Subscriber::getAddressesFromGroup($id);
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

                if (Subscriber::emailInGroup($id, $model->emailAddress)){
                    $emailId = SubscriberEmail::findByEmail($model->emailAddress);
                    $subscriber = new Subscriber();
                    $subscriber->group_id = $id;
                    $subscriber->email_id = $emailId->id;
                    $subscriber->save();
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
                    // CSV súbor
                    if (strtolower($file->extension) == 'csv'){
                    define('CSV_PATH','uploads/');
                    $csv_file = CSV_PATH . $filename;
                    $filecsv = file($csv_file);
                    foreach ($filecsv as $data) {
                        $line = explode(",", $data);
                        foreach ($line as $key => $value) {
                            $value = trim($value);
                            if(filter_var($value, FILTER_VALIDATE_EMAIL)){
                                if (Subscriber::emailInGroup($id, $value)){
                                    if (SubscriberEmail::findByEmail($value) == null){
                                        $newEmail = new SubscriberEmail();
                                        $newEmail->email = $value;
                                        $newEmail->save();
                                        $emailId = $newEmail->id;
                                    }
                                    else {
                                        $emailId = SubscriberEmail::findByEmail($email)->id;
                                    }

                                    $newEmailInGroup = new Subscriber();
                                    $newEmailInGroup->group_id = $id;
                                    $newEmailInGroup->email_id = $emailId;
                                    $newEmailInGroup->save();
                                }
                            }
                        } 
                    }
                    unlink('uploads/'.$filename);
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

                    foreach ($values as $key => $value) {
                        if (strtolower($value['tag']) == "email" or strtolower($value['tag']) == 'e-mail') {
                            $address = trim($value['value']);
                            if(filter_var($address, FILTER_VALIDATE_EMAIL)){
                                if (Subscriber::emailInGroup($id, $address)){
                                    if (SubscriberEmail::findByEmail($address) == null){
                                        $newEmail = new SubscriberEmail();
                                        $newEmail->email = $address;
                                        $newEmail->save();
                                        $emailId = $newEmail->id;
                                    }
                                    else {
                                        $emailId = SubscriberEmail::findByEmail($email)->id;
                                    }

                                    $newEmailInGroup = new Subscriber();
                                    $newEmailInGroup->group_id = $id;
                                    $newEmailInGroup->email_id = $emailId;
                                    $newEmailInGroup->save();
                                }
                            }
                        }
                    }

                    unlink('uploads/'.$filename);
                    return $this->refresh();
                    }
                }
            }

            return $this->render('show', 
                array('model'=>$model, 'modelImport'=>$modelImport, 'modelExport'=>$modelExport, 'items'=>$items,
                        'addresses' => $addresses, 'groupInfo' => $groupInfo));
        }

        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }
}
