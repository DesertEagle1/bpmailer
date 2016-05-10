<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\AccessRights;
use app\models\NewUserForm;
use app\models\EditUser;
use yii\data\ActiveDataProvider;

class AdminController extends Controller
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

    public function actionUsers()
    {
        $rights = AccessRights::getAccessRights(Yii::$app->user->id);
        $result = array();
        foreach ($rights as $key => $value) {
             $result[] = $rights[$key]['access_right_id'];
         } 
        Yii::$app->view->params['accessRightsArray'] = $result;

        $allRights = AccessRights::getAllAccessRights();

        $usersIdList = User::getUsersIds();

        if (in_array(1, $result)) {
            return $this->render('users', array('allRights' => $allRights, 'usersIdList'=>$usersIdList));
        }
        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

    public function actionLogs()
    {
        $rights = AccessRights::getAccessRights(Yii::$app->user->id);
        $result = array();
        foreach ($rights as $key => $value) {
             $result[] = $rights[$key]['access_right_id'];
         } 
        Yii::$app->view->params['accessRightsArray'] = $result;

        if (in_array(1, $result)) {
            return $this->render('logs');
        }
        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

    public function actionNewuser()
    {
        $rights = AccessRights::getAccessRights(Yii::$app->user->id);
        $result = array();
        foreach ($rights as $key => $value) {
             $result[] = $rights[$key]['access_right_id'];
         } 
        Yii::$app->view->params['accessRightsArray'] = $result;

        if (in_array(1, $result)) {

            $model = new NewUserForm();
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $user = new User();
                $user->username = $model->username;
                $user->password_hash = Yii::$app->getSecurity()->generatePasswordHash($model->password);
                $user->save();

                if (User::findByUsername($model->username) != null){
                    $id = User::findByUsername($model->username)['id'];
                    $rights = new AccessRights();
                    if ($model->admin){
                        $rights->user_id = $id;
                        $rights->access_right_id = 1;
                        $rights->save();
                    }

                    $rights = new AccessRights();
                    if ($model->newsletterAccess){
                        $rights->user_id = $id;
                        $rights->access_right_id = 2;
                        $rights->save();
                    }
                    
                    $rights = new AccessRights();

                    if ($model->groupAccess){
                        $rights->user_id = $id;
                        $rights->access_right_id = 3;
                        $rights->save();
                    }

                    $rights = new AccessRights();
                    if ($model->templateAccess){
                        $rights->user_id = $id;
                        $rights->access_right_id = 4;
                        $rights->save();
                    }
                    
                }

                $this->refresh();
            }
            return $this->render('newuser', array('model' => $model));
        }
        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

    public function actionEdituser($id)
    {
        $rights = AccessRights::getAccessRights(Yii::$app->user->id);
        $result = array();
        foreach ($rights as $key => $value) {
             $result[] = $rights[$key]['access_right_id'];
         } 
        Yii::$app->view->params['accessRightsArray'] = $result;

        $rights_id = AccessRights::getAccessRights($id);
        $accessRights = array();
        foreach ($rights_id as $key => $value) {
            $accessRights[] = $rights_id[$key]['access_right_id'];
        }

        if ($id == Yii::$app->user->id){
             return $this->render('error', array('name'=>'Nepovolený prístup', 
                                                'message'=>'Nemôžete upravovať vlastné prístupové práva.'));
        }

        $username = User::findIdentity($id);
        if ($username == null){
            return $this->render('error', array('name'=>'Používateľ neexistuje', 
                                                'message'=>'Používateľ so zadaným ID neexistuje.'));
        }
        $username = $username->username;

        if (in_array(1, $result)) {
            $model = new EditUser();
            in_array(1, $accessRights) ? $model->admin = 1 : $model->admin = 0;
            in_array(2, $accessRights) ? $model->newsletterAccess = 1 : $model->newsletterAccess = 0;
            in_array(3, $accessRights) ? $model->groupAccess = 1 : $model->groupAccess = 0;
            in_array(4, $accessRights) ? $model->templateAccess = 1 : $model->templateAccess = 0;

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {

                // admin prava
                if ($model->admin && !in_array(1, $accessRights)) {
                    AccessRights::createNewAccessRight($id, 1);
                }
                if (!$model->admin && in_array(1, $accessRights)) {
                    AccessRights::deleteAccessRight($id, 1);
                }

                // pravo na upravu newsletterov
                if ($model->newsletterAccess && !in_array(2, $accessRights)) {
                    AccessRights::createNewAccessRight($id, 2);
                }
                if (!$model->newsletterAccess && in_array(2, $accessRights)) {
                    AccessRights::deleteAccessRight($id, 2);
                }

                // pravo na skupiny
                if ($model->groupAccess && !in_array(3, $accessRights)) {
                    AccessRights::createNewAccessRight($id, 3);
                }
                if (!$model->groupAccess && in_array(3, $accessRights)) {
                    AccessRights::deleteAccessRight($id, 3);
                }

                // pravo na sablony
                if ($model->templateAccess && !in_array(4, $accessRights)) {
                    AccessRights::createNewAccessRight($id, 4);
                }
                if (!$model->templateAccess && in_array(4, $accessRights)) {
                    AccessRights::deleteAccessRight($id, 4);
                }

               return $this->refresh();
            }
            return $this->render('edituser', array('model' => $model, 'accessRights' => $accessRights, 'username' => $username));
        }
        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }
}
