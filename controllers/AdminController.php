<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\AccessRights;
use app\models\NewUserForm;
use app\models\EditUserForm;
use app\models\Log;

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
        $rights = AccessRights::getAccessRightsForMenu(Yii::$app->user->id);
        Yii::$app->view->params['accessRightsArray'] = $rights;

        $allRights = AccessRights::getAllAccessRights();

        $usersIdList = User::getUsersIds();

        if (in_array(1, $rights)) {
            return $this->render('users', array('allRights' => $allRights, 'usersIdList'=>$usersIdList));
        }
        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

    public function actionLogs($page = 0)
    {
        $rights = AccessRights::getAccessRightsForMenu(Yii::$app->user->id);
        Yii::$app->view->params['accessRightsArray'] = $rights;

        if (in_array(1, $rights)) {
            
            $query = Log::getLogs();
            $count = $query->count();
            $pagination = new Pagination(['totalCount' => $count]);
            $logs = $query->offset($pagination->offset)
                        ->limit($pagination->limit)
                        ->all();
            return $this->render('logs', array('logs' => $logs, 'pagination' => $pagination));
        }
        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

    public function actionNewuser()
    {
        $rights = AccessRights::getAccessRightsForMenu(Yii::$app->user->id);
        Yii::$app->view->params['accessRightsArray'] = $rights;

        if (in_array(1, $rights)) {

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
                Log::writeLog(Yii::$app->user->id, 11, $model->username);
                Yii::$app->session->setFlash('success', 'Používateľ ' . $model->username .' bol úspešne vytvorený.');
                return $this->redirect(Url::to(['admin/users']));
                
            }
            return $this->render('newuser', array('model' => $model));
        }
        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

    public function actionEdituser($id)
    {
        $rights = AccessRights::getAccessRightsForMenu(Yii::$app->user->id);
        Yii::$app->view->params['accessRightsArray'] = $rights;

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

        if (in_array(1, $rights)) {
            $model = new EditUserForm();
            in_array(1, $accessRights) ? $model->admin = true : $model->admin = false;
            in_array(2, $accessRights) ? $model->newsletterAccess = true : $model->newsletterAccess = false;
            in_array(3, $accessRights) ? $model->groupAccess = true : $model->groupAccess = false;
            in_array(4, $accessRights) ? $model->templateAccess = true : $model->templateAccess = false;

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

                Log::writeLog(Yii::$app->user->id, 12, $username);
                Yii::$app->session->setFlash('success', 'Používateľské práva boli zmenené.');
                return $this->refresh();
            }
            return $this->render('edituser', array('model' => $model, 'accessRights' => $accessRights, 'username' => $username));
        }
        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }
}
