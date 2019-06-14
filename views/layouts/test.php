<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\assets\AppBottomAsset;
use app\assets\AppTestAsset;

AppTestAsset::register($this);
//AppBottomAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="icon" href="/i/favicon.png">
    <?php $this->head() ?>
<body>
<?php $this->beginBody() ?>

    <div class="tour-selection-box">

        <?= Alert::widget() ?>
        <?= $content ?>

    </div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
