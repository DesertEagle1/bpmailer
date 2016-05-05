<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\NewsletterForm;
use app\models\Newsletter;
use app\models\AccessRights;
use app\models\Group;
use app\models\Subscriber;
use app\models\Template;
use app\models\UploadFiles;
use app\models\File;

class NewsletterController extends Controller
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

        if (!empty(array_intersect([1,2], $result))) {
            $model = new NewsletterForm();
            $modelUpload = new UploadFiles();
            $groups = Group::getGroupsWithIds();
            $templates = Template::getTemplatesWithIds();
            $sourceCodes = Template::getSourceCodes();

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $newsletter = new Newsletter();
                $newsletter->subject = $model->subject;
                $newsletter->sent_from = $model->sentFrom;
                $newsletter->send_to_group = $model->receivers;
                $newsletter->copy_to = $model->copyTo;
                $newsletter->reply_to = $model->replyTo;
                $newsletter->content = $model->content;
                $newsletter->created_by = Yii::$app->user->id;
                $newsletter->status = 1;
                $newsletter->save();

                $currentID = $newsletter->id;
                $modelUpload->attachments = UploadedFile::getInstances($modelUpload, 'attachments');
                if ($modelUpload->upload($currentID)) {
                    // Success
                }
            }

            return $this->render('new', array('model' => $model, 'modelUpload' => $modelUpload, 
                                            'groups'=>$groups, 'templates'=>$templates, 'sourceCodes'=>$sourceCodes));
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

        if (!empty(array_intersect([1,2], $result))) {
            $newsletters = Newsletter::getAllNewsletters();
            return $this->render('all', array('newsletters' => $newsletters));
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

        if (!empty(array_intersect([1,2], $result))) {

            return $this->render('show', 
                array());
        }

        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

    public function actionSend($id)
    {
        $rights = AccessRights::getAccessRights(Yii::$app->user->id);
        $result = array();
        foreach ($rights as $key => $value) {
             $result[] = $rights[$key]['access_right_id'];
         } 
        Yii::$app->view->params['accessRightsArray'] = $result;

        if (!empty(array_intersect([1,2], $result))) {
            $model = Newsletter::findById($id);
            $subscribersCount = Subscriber::countSubscribers($model->send_to_group) + sizeof(explode(",", $model->copy_to));
            $attachments = File::findByNewsletterId($id);
            return $this->render('send', array('model' => $model, 'subscribersCount' => $subscribersCount, 
                                                'attachments' => $attachments));     
        }

        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

}
