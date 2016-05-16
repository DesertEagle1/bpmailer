<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use app\models\User;
use app\models\NewsletterForm;
use app\models\SendNewsletterForm;
use app\models\Newsletter;
use app\models\AccessRights;
use app\models\Group;
use app\models\Subscriber;
use app\models\Template;
use app\models\UploadFiles;
use app\models\File;
use app\models\Log;

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
                $modelUpload->upload($currentID);

                Log::writeLog(Yii::$app->user->id, 2, $model->subject);
                
                return $this->redirect(Url::to(['newsletter/all']));
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
            $model = Newsletter::findById($id);
            $subscribersCount = Subscriber::countSubscribers($model->send_to_group) + sizeof(explode(",", $model->copy_to));
            $attachments = File::findByNewsletterId($id);
            
            return $this->render('show', array('model' => $model, 'subscribersCount' => $subscribersCount,
                                'attachments' => $attachments));
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

            if ($model == null){
                return $this->render('error', array('name'=>'Neznámy newsletter', 'message'=>'Newsletter so zadaným ID neexistuje.'));
            }

            if ($model->status != 1 ){
                return $this->render('error', array('name'=>'Už odoslaný', 'message'=>'Newsletter už bol odoslaný.'));
            }

            $subscribersCount = Subscriber::countSubscribers($model->send_to_group) + sizeof(explode(",", $model->copy_to));
            $attachments = File::findByNewsletterId($id);

            $modelSend = new SendNewsletterForm();
            if ($modelSend->load(Yii::$app->request->post())){
                $addresses = Subscriber::getAddressesFromGroup($model->send_to_group);
                $message = Yii::$app->mailer->compose();
                $message->setFrom('company@company.com');
                $message->setTo('company@company.com');
                if ($model->copy_to != null){
                    $message->setCc(explode(",", $model->copy_to));
                }
                $message->setBcc($addresses);
                if ($model->reply_to != null){
                    $message->setReplyTo($model->reply_to);
                }
                $message->setSubject($model->subject);
                $message->setHtmlBody($model->content);

                $files = File::findByNewsletterId($id);
                foreach ($files as $key => $value) {
                    $message->attach('files/' . $value['filename_hash']);
                }

                $message->send();

                $model->status = 2;
                $model->sent_at = date('Y-m-d H:i:s');
                $model->update();

                Log::writeLog(Yii::$app->user->id, 3, $model->subject);

                Yii::$app->session->setFlash('success', 'Newsletter sa podarilo úspešne odoslať.');
                return $this->redirect(Url::to(['newsletter/show', 'id'=> $id]));
            }

            return $this->render('send', array('model' => $model, 'subscribersCount' => $subscribersCount, 
                                                'attachments' => $attachments, 'modelSend' => $modelSend,
                                                ));     
        }

        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

}
