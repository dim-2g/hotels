<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>



<?php $this->registerJs("
if (typeof lsfw.bookingRequest === 'undefined') {
    lsfw.bookingRequest = JSON.parse(JSON.stringify(lsfw.ui.main.request));
}

");

?>

<?= \LibUiTourFilter\widgets\WPrice::widget([
    'name' => 'prix',
    'templateId' => '_',
    'cssClass' => 'tour-selection-field tour-selection-field--price',
    'jsReqObject' => 'lsfw.bookingRequest',
    'jsFormObject' => 'var formPrix',
    'priceFrom' => 0,
    'priceTo' => 0,
    'priceComfort' => 100000,
    'forceShowPrice' => true,
]); ?>

<? $this->registerJs('formPrix.forceShowPrice = true;') ?>
<? $this->registerJs('formPrix.reloadPriceLabel();') ?>
