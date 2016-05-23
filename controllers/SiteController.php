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
use app\models\Newsletter;
use app\models\Subscriber;

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
          
        $rights = AccessRights::getAccessRightsForMenu(Yii::$app->user->id);
        Yii::$app->view->params['accessRightsArray'] = $rights;

        $newsletters = Newsletter::getAllNewsletters();
        $newsletters = array_slice($newsletters, 0, 3);
        $todaySubscribers = array_unique(Subscriber::getTodaySubscribers());

        $lineChartData = array(array('Mesiac', 'Noví odberatelia'),
                               array('December 2015', 55),
                               array('January 2016', 79),
                               array('February 2016', 101),
                               array('March 2016', 90),
                               array('April 2016', 87),
                               array('May 2016', 61),
                               );
        /*for ($i=5; $i >= 0; $i--) { 
            $from = date('Y-m-01', strtotime(date('Y-m-d') . "-" . $i . " months"));
            $to = date('Y-m-t', strtotime(date('Y-m-d') . "-" . $i . " months"));;
            $lineChartData[] = array(date('F Y',strtotime($from)), 
                                     intval(Subscriber::getSubscribersBetweenDates($from, $to))); 
        }*/

        $columnChartData = array(array('Mesiac', 'Odoslané newslettere'));
        for ($i=5; $i >= 0; $i--) { 
            $from = date('Y-m-01', strtotime(date('Y-m-d') . "-" . $i . " months"));
            $to = date('Y-m-t', strtotime(date('Y-m-d') . "-" . $i . " months"));;
            $columnChartData[] = array(date('F Y',strtotime($from)), 
                                     intval(Newsletter::getNewslettersBetweenDates($from, $to))); 
        }

        return $this->render('index', array('model'=>$model, 'newsletters' => $newsletters, 
                                            'todaySubscribers' => $todaySubscribers, 'lineChartData' => $lineChartData,
                                            'columnChartData' => $columnChartData));
        
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
        $rights = AccessRights::getAccessRightsForMenu(Yii::$app->user->id);
        Yii::$app->view->params['accessRightsArray'] = $rights;
        
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
