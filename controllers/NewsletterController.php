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
use app\models\SubscriberEmail;
use app\models\Template;
use app\models\UploadFiles;
use app\models\File;
use app\models\Log;
use app\models\Stats;

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
        $rights = AccessRights::getAccessRightsForMenu(Yii::$app->user->id);
        Yii::$app->view->params['accessRightsArray'] = $rights;

        if (!empty(array_intersect([1,2], $rights))) {
            $model = new NewsletterForm();
            $modelUpload = new UploadFiles();
            $groups = Group::getGroupsWithIds();
            $templates = Template::getTemplatesWithIds();

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
                                            'groups'=>$groups, 'templates'=>$templates));
        }

        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

    public function actionAll()
    {
        $rights = AccessRights::getAccessRightsForMenu(Yii::$app->user->id);
        Yii::$app->view->params['accessRightsArray'] = $rights;

        if (!empty(array_intersect([1,2], $rights))) {
            $newsletters = Newsletter::getAllNewsletters();
            return $this->render('all', array('newsletters' => $newsletters));
        }

        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

    public function actionShow($id)
    {
        $rights = AccessRights::getAccessRightsForMenu(Yii::$app->user->id);
        Yii::$app->view->params['accessRightsArray'] = $rights;

        if (!empty(array_intersect([1,2], $rights))) {
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
       
        $rights = AccessRights::getAccessRightsForMenu(Yii::$app->user->id);
        Yii::$app->view->params['accessRightsArray'] = $rights;

        if (!empty(array_intersect([1,2], $rights))) {
            $model = Newsletter::findById($id);

            if ($model == null){
                return $this->render('error', array('name'=>'Neznámy newsletter', 'message'=>'Newsletter so zadaným ID neexistuje.'));
            }

            if ($model->status != 1 ){
                return $this->render('error', array('name'=>'Už odoslaný', 'message'=>'Newsletter už bol odoslaný.'));
            }

            if ($model->copy_to) {
                $numberofCopies = sizeof(explode(",", $value['copy_to']));
            }
            else {
                $numberofCopies = 0;
            }
            $subscribersCount = Subscriber::countSubscribers($model->send_to_group) + $numberofCopies;
            $attachments = File::findByNewsletterId($id);

            $modelSend = new SendNewsletterForm();
            if ($modelSend->load(Yii::$app->request->post())){

                $addresses = Subscriber::getAddressesFromGroup($model->send_to_group);
                if ($model->copy_to != null){
                    $copies = explode(",", $model->copy_to);
                    foreach ($copies as $key => $value) {
                        $addresses[] = $value;
                    }
                }

                $messages = [];
                foreach ($addresses as $address) {
                    $message = Yii::$app->mailer->compose();
                    $message->setFrom('company@company.com');
                    $message->setTo($address);
                    if ($model->reply_to != null){
                        $message->setReplyTo($model->reply_to);
                    }
                    $message->setSubject($model->subject);
                    $token = Subscriber::findToken($address, $model->send_to_group);
                    $content = $this->personalize($model->content, $token, $id);
                    $message->setHtmlBody($content);
                    $files = File::findByNewsletterId($id);
                    foreach ($files as $key => $value) {
                        $message->attach('files/' . $value['filename_hash']);
                    }
                    $messages[] = $message;
                }
                $successful = Yii::$app->mailer->sendMultiple($messages);

                $model->status = 2;
                $model->sent_at = date('Y-m-d H:i:s');
                $model->update();

                $newStat = new Stats();
                $newStat->newsletter_id = $id;
                $newStat->receivers = $successful . '/' . sizeof($messages);
                $newStat->save();

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

    public function actionEdit($id)
    {
        $rights = AccessRights::getAccessRightsForMenu(Yii::$app->user->id);
        Yii::$app->view->params['accessRightsArray'] = $rights;

        if (!empty(array_intersect([1,2], $rights))) {
            $newsletter = Newsletter::findOne(['id' => $id]);
            
            if ($newsletter === null){
                return $this->render('error', array('name'=>'Newsletter neexistuje', 'message'=>'Newsletter so zadaným ID neexistuje.'));
            }

            if ($newsletter->status != 1){
                return $this->render('error', array('name'=>'Newsletter sa nedá upraviť', 'message'=>'Newsletter so zadaným ID bol už odoslaný alebo zrušený.'));
            }

            $model = new NewsletterForm();
            $modelUpload = new UploadFiles();
            $groups = Group::getGroupsWithIds();
            $templates = Template::getTemplatesWithIds();
            $attachments = File::findByNewsletterId($id);
            $model->subject = $newsletter->subject;
            $model->receivers = $newsletter->send_to_group;
            $model->copyTo = $newsletter->copy_to;
            $model->sentFrom = $newsletter->sent_from;
            $model->replyTo = $newsletter->reply_to;
            $model->content = $newsletter->content;

            if ($model->load(Yii::$app->request->post()) && $model->validate()){
                $newsletter = Newsletter::findById($id);
                $newsletter->subject = $model->subject;
                $newsletter->send_to_group = $model->receivers;
                $newsletter->copy_to = $model->copyTo;
                $newsletter->sent_from = $model->sentFrom;
                $newsletter->reply_to = $model->replyTo;
                $newsletter->content = $model->content;
                $newsletter->update();

                $currentID = $newsletter->id;
                $modelUpload->attachments = UploadedFile::getInstances($modelUpload, 'attachments');
                $modelUpload->upload($currentID);

                Log::writeLog(Yii::$app->user->id, 8, $newsletter->subject);

                $this->refresh();
            }
            return $this->render('edit', array('model' => $model, 'templates' => $templates, 'attachments' => $attachments,
                                               'groups' => $groups, 'modelUpload' => $modelUpload));
        }

        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

    public function personalize($message, $token, $nid){      
        
        preg_match_all('!https?://\S+\"!', $message, $matches);
        $all_urls = $matches[0];
        foreach ($all_urls as $key => $value) {
            $all_urls[$key] = substr($value, 0, strlen($value)-1);
        }

        foreach ($all_urls as $key => $value) {
            $newUrl = Url::to(['api/linkclicked', 'token'=> $token, 'nid'=> $nid, 'target' => urlencode($value)], true);
            $message = str_replace($value, $newUrl, $message);
        }

        $script = '<span style="background-image: url(' . Url::to(['api/mailopen', 'token'=> $token, 'nid'=> $nid], true) .')"></span>';
        $message = $message . $script;

        return $message;
    }

}
