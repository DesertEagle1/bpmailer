<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
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
    <link href="<?= Url::to(['css/custom.css'], true) ?>" rel="stylesheet">
    <link rel="shortcut icon" href="<?= Url::to(['/favicon.ico'], true) ?>" type="image/x-icon" />
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
                            ['label' => 'Vytvoriť nový newsletter', 'url' => Url::to(['newsletter/new'])] : null,
                            ['label' => 'Prehľad newsletterov', 'url' => Url::to(['newsletter/all'])],
                             ];

        $groupNavItems = ((in_array(3, $this->params['accessRightsArray']))
                            or (in_array(1, $this->params['accessRightsArray']))) ? 
                        [['label' => 'Vytvoriť novú skupinu', 'url' => Url::to(['group/new'])],
                        ['label' => 'Prehľad skupín', 'url' => Url::to(['group/all'])]] : null; 

        $templateNavItems = ((in_array(4, $this->params['accessRightsArray'])) 
                            or (in_array(1, $this->params['accessRightsArray'])))? 
                        [['label' => 'Vytvoriť novú šablónu', 'url' => Url::to(['template/new'])],
                        ['label' => 'Prehľad šablón', 'url' => Url::to(['template/all'])]] : null; 

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
                        ['label' => 'Používatelia', 'url' => Url::to(['admin/users'])],
                        ['label' => 'Logy', 'url' => Url::to(['admin/logs'])],
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
                    ['label' => 'Profil', 'url' => Url::to(['site/profile'])],
                    ['label' => 'Zmeniť heslo', 'url' => Url::to(['site/changepassword'])],
                    '<li role="separator" class="divider"></li>',
                    '<li>'
                    . Html::beginForm(['/site/logout'], 'post')
                    . Html::submitButton(
                        '<span class="glyphicon glyphicon-off" aria-hidden="true"></span> Odhlásiť (' . Yii::$app->user->identity->username . ')',
                        ['class' => 'btn btn-link',
                        'id' => 'logout-button']
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
