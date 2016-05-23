<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\AccessRights;
use app\models\Template;
use app\models\NewTemplateForm;
use app\models\EditTemplateForm;
use app\models\Log;

class TemplateController extends Controller
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

        if (!empty(array_intersect([1,4], $rights))) {
            $model = new NewTemplateForm();
            if ($model->load(Yii::$app->request->post()) && $model->validate()){
                $template = new Template();
                $template->template_name = $model->name;
                $template->source_code = $model->sourceCode;
                $template->save();

                Log::writeLog(Yii::$app->user->id, 9, $model->name);
            }
            return $this->render('new', array('model' => $model));
        }

        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

    public function actionAll()
    {
        $rights = AccessRights::getAccessRightsForMenu(Yii::$app->user->id);
        Yii::$app->view->params['accessRightsArray'] = $rights;

        if (!empty(array_intersect([1,4], $rights))) {
            $templates = Template::find()->all();
            return $this->render('all', array('templates' => $templates));
        }

        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }

    public function actionShow($id)
    {
        $rights = AccessRights::getAccessRightsForMenu(Yii::$app->user->id);
        Yii::$app->view->params['accessRightsArray'] = $rights;

        if (!empty(array_intersect([1,4], $rights))) {
            $template = Template::findOne(['id' => $id]);
            
            if ($template === null){
                return $this->render('error', array('name'=>'Šablóna neexistuje', 'message'=>'Šablóna so zadaným ID neexistuje.'));
            }

            $model = new EditTemplateForm();
            $model->sourceCode = $template->source_code;
            if ($model->load(Yii::$app->request->post()) && $model->validate()){
                $template = Template::findById($id);
                $template->source_code = $model->sourceCode;
                $template->update();

                Log::writeLog(Yii::$app->user->id, 10, $template->template_name);
            }
            return $this->render('show', array('model' => $model, 'template' => $template));
        }

        return $this->render('error', array('name'=>'Nepovolený prístup', 'message'=>'Do tejto časti nemáte prístup!'));
    }
}