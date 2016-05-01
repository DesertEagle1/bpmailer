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
            $modelImport = new SubscriberFormImport();
            $modelExport = new SubscriberFormExport();
            $items = array('csv'=>'.CSV', 'xml'=>'.XML');

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
            }

            return $this->render('show', 
                array('model'=>$model, 'modelImport'=>$modelImport, 'modelExport'=>$modelExport, 'items'=>$items));
        }

        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }
}
