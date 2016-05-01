<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>

    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link href="css/custom.css" rel="stylesheet">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '<span class="glyphicon glyphicon-home" aria-hidden="true"></span> Mailer',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    if (!\Yii::$app->user->isGuest) {

        $newsletterNavItems = [((in_array(2, $this->params['accessRightsArray'])) 
                            or (in_array(1, $this->params['accessRightsArray']))) ? 
                            ['label' => 'Vytvoriť nový newsletter', 'url' => '?r=newsletter%2Fnew'] : null,
                            ['label' => 'Prehľad newsletterov', 'url' => '?r=newsletter%2Fall'],
                             ];

        $groupNavItems = ((in_array(3, $this->params['accessRightsArray']))
                            or (in_array(1, $this->params['accessRightsArray']))) ? 
                        [['label' => 'Vytvoriť novú skupinu', 'url' => '?r=group%2Fnew'],
                        ['label' => 'Prehľad skupín', 'url' => '?r=group%2Fall']] : null; 

        $templateNavItems = ((in_array(4, $this->params['accessRightsArray'])) 
                            or (in_array(1, $this->params['accessRightsArray'])))? 
                        [['label' => 'Vytvoriť novú šablónu', 'url' => '?r=template%2Fnew'],
                        ['label' => 'Upraviť existujúce šablóny', 'url' => '?r=template%2Fall']] : null; 

        $navItems =  [
                [
                    'label' => 'Newslettere',
                    'url' => '#',
                    'items' => array_filter($newsletterNavItems),
                ],

                sizeof($groupNavItems)>0 ? 
                [
                    'label' => 'Skupiny odberateľov',
                    'url' => '#',
                    'items' => array_filter($groupNavItems),
                ] : null,

                sizeof($templateNavItems)>0 ?
                [
                    'label' => 'Šablóny',
                    'url' => '#',
                    'items' => array_filter($templateNavItems),
                ] : null,

                (in_array(1, $this->params['accessRightsArray'])) ?
                [
                    'label' => 'Administrácia',
                    'url' => '#',
                    'items' => [
                        ['label' => 'Používatelia', 'url' => '?r=admin%2Fusers'],
                        ['label' => 'Logy', 'url' => '?r=admin%2Flogs'],
                    ],
                ] : null,
            ];
        echo Nav::widget([
            'options' => [
                'class' => 'navbar-nav navbar-left',
            ],
            'items' => array_filter($navItems)
        ]);
    }

    if (!\Yii::$app->user->isGuest) {
        echo Nav::widget([
            'encodeLabels' => false,
            'options' => [
                'class' => 'navbar-nav navbar-right',
            ],
            'items' => [
            [
                'label' => Yii::$app->user->identity->username,
                'url' => '#',
                'items' => [
                    ['label' => 'Profil', 'url' => '?r=site%2Fprofile'],
                    ['label' => 'Zmeniť heslo', 'url' => '?r=site%2Fchangepassword'],
                    '<li role="separator" class="divider"></li>',
                    '<li>'
                    . Html::beginForm(['/site/logout'], 'post')
                    . Html::submitButton(
                        '<span class="glyphicon glyphicon-off" aria-hidden="true"></span> Odhlásiť (' . Yii::$app->user->identity->username . ')',
                        ['class' => 'btn btn-link']
                    )
                    . Html::endForm()
                    . '</li>',
                ],
            ],
            ]
        ]);
    }
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Peter Gubik <?= date('Y') ?></p>
        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
