<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\helpers\Url;
use yii\filters\VerbFilter;
use app\models\Subscriber;
use app\models\SubscriberEmail;
use app\models\Stats;
use app\models\StatsSeen;
use app\models\StatsClicks;
use app\models\Template;

class ApiController extends Controller
{

    public function actionUnsubscribe($email = '', $token = '')
    {
        if ($email == '' or $token == ''){
            return ;
        }
        $email = urldecode($email);
        $emailAddress = SubscriberEmail::findByEmail($email);
        if ($emailAddress){
            $emailId = $emailAddress->id;
            $deletedSubscriber = Subscriber::find()
                ->where(['group_id' => 1, 
                        'email_id' => $emailId,
                        'token' => $token])
                ->one();

            if ($deletedSubscriber != null){
                $deletedSubscriber->delete();
            }

            $exists = Subscriber::find()
                    ->where(['email_id' => $emailId])
                    ->count();
            if ($exists == 0){
                $deletedAddress = SubscriberEmail::find()
                        ->where(['email' => $email])
                        ->one();
                $deletedAddress->delete();
            }
        }
    }

    public function actionSubscribe($email = '')
    {
        if ($email == ''){
            return ;
        }
        $email = urldecode($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ;
        }
        $emailAddress = SubscriberEmail::findByEmail($email);
        if ($emailAddress){
            $emailId = $emailAddress->id;
            $exists = Subscriber::find()
                    ->where(['email_id' => $emailId,
                             'group_id' => 1])
                    ->count();

            if ($exists == 0){
                $record = new Subscriber();
                $record->email_id = $emailId;
                $record->group_id = 1;
                $record->save();
            }
        }
        else {
            $record = new SubscriberEmail();
            $record->email = $email;
            $record->save();

            $emailId = $record->id;
            $record = new Subscriber();
            $record->email_id = $emailId;
            $record->group_id = 1;
            $randomString = Yii::$app->getSecurity()->generateRandomString();
            while (Subscriber::find()->where(['token' => $randomString])->one()) {
                $randomString = Yii::$app->getSecurity()->generateRandomString();
            }
            $record->token = $randomString;
            $record->save();
        }
    }

    public function actionMailopen($token, $nid)
    {
        $subscriber = Subscriber::find()
                        ->where(['token' => $token])
                        ->one();

        $statistic = Stats::find()
                        ->where(['newsletter_id'=>$nid])
                        ->one();

        if ($statistic === null or $subscriber === null) {
            return ;
        }

        $stat_id = $statistic->id;
        $exists = StatsSeen::find()
                ->where(['statistic_id'=>$stat_id, 'subscriber_id'=>$subscriber->id])
                ->one();
        if ($exists) {
            return ;
        }
        $record = new StatsSeen();
        $record->statistic_id = $stat_id;
        $record->subscriber_id = $subscriber->id;
        $record->save();
        return ;
    }

    public function actionLinkclicked($token, $nid, $target)
    {
        $subscriber = Subscriber::find()
                        ->where(['token' => $token])
                        ->one();

        $statistic = Stats::find()
                        ->where(['newsletter_id'=>$nid])
                        ->one();

        if ($statistic === null or $subscriber === null) {
            return $this->redirect(urldecode($target));
        }

        $stat_id = $statistic->id;
        $record = new StatsClicks();
        $record->statistic_id = $stat_id;
        $record->subscriber_id = $subscriber->id;
        $record->save();
        return $this->redirect(urldecode($target));
    }

    public function actionTemplate($id)
    {
        $template = Template::find()
                    ->where(['id' => $id])
                    ->one();

        return $this->renderPartial('template', array('template' => $template));
    }

}