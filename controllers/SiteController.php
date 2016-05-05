<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\LoginForm;
use app\models\AccessRights;
use app\models\ChangePasswordForm;

class SiteController extends Controller
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

    public function actionIndex()
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        else
        {   
            $rights = AccessRights::getAccessRights(Yii::$app->user->id);
            $result = array();
            foreach ($rights as $key => $value) {
                 $result[] = $rights[$key]['access_right_id'];
             } 
            Yii::$app->view->params['accessRightsArray'] = $result;
            return $this->render('index', array('model'=>$model));
        }
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

    public function actionChangepassword()
    {
        $rights = AccessRights::getAccessRights(Yii::$app->user->id);
        $result = array();
        foreach ($rights as $key => $value) {
             $result[] = $rights[$key]['access_right_id'];
         } 
        Yii::$app->view->params['accessRightsArray'] = $result;
        
        if (!\Yii::$app->user->isGuest){
            $model = new ChangePasswordForm();
            if ($model->load(Yii::$app->request->post()) && $model->validate()){
                $user = User::findOne(Yii::$app->user->identity->id);
                $user->password_hash = Yii::$app->getSecurity()->generatePasswordHash($model->newPassword);
                $user->update();
            }
            return $this->render('changepassword', array('model'=>$model));
        }
        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }
}
