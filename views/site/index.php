<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'TopHotels';
?>
<div class="tabs-block">
    <div class="tabs-bar   tabs-bar--responsive js-768-tabs">
        <div id="step1" class="tab active">Подобрать тур</div>
        <div id="form" class="tab">Нестандартный запрос</div>
        <div class="line" style="width: 130px"></div>
    </div>


    <div class="panel" id="step1Panel">
        <?php
        $form = ActiveForm::begin([
        'id' => 'booking-form',
        'options' => ['class' => ''],
        ]) ?>
        <div class="bth__cnt uppercase">Пожалуйста, укажите параметры вашей поездки</div>
        
        <div class="tour-selection-wrap">
            <div class="tour-selection-wrap-in tour-selection-wrap-flex">

                <?php $this->registerJs("
                if (typeof lsfw.bookingRequest === 'undefined') {
                    lsfw.bookingRequest = JSON.parse(JSON.stringify(lsfw.ui.main.request));
                    lsfw.bookingRequest.pt = 0;
                }

                "); ?>

                <?= \LibUiTourFilter\widgets\WDate::widget([
                    'name' => 'date',
                    'templateId' => '_',
                    'cssClass' => 'tour-selection-field tour-selection-field--250',
                    'jsReqObject' => 'lsfw.bookingRequest',
                    'jsFormObject' => 'var formDate',
                    'dateFrom' => $data['dateFrom'],
                    'dateTo' => $data['dateTo'],
                    //'dateConfig' => ['startDate' => '2019-06-14']
                ]); ?>

                <?= \LibUiTourFilter\widgets\WNights::widget([
                    'name' => 'duration',
                    'templateId' => '_',
                    'cssClass' => 'tour-selection-field tour-selection-field--250',
                    'jsReqObject' => 'lsfw.bookingRequest',
                    'jsFormObject' => 'var formDuration',
                    'nightFrom' => $data['nightFrom'],
                    'nightTo' => $data['nightTo'],
                ]); ?>


                <?= \LibUiTourFilter\widgets\WGuest::widget([
                    'name' => 'guest',
                    'templateId' => '_',
                    'cssClass' => 'tour-selection-field tour-selection-field--250',
                    'jsReqObject' => 'lsfw.bookingRequest',
                    'jsFormObject' => 'var formGuest',
                    'adults' => 2,
                    'children' => 0,
                ]); ?>

                <?= \LibUiTourFilter\widgets\WPrice::widget([
                    'name' => 'prix',
                    'templateId' => '_',
                    'cssClass' => 'tour-selection-field tour-selection-field--price',
                    'jsReqObject' => 'lsfw.bookingRequest',
                    'jsFormObject' => 'var formPrix',
                    'priceFrom' => 0,
                    'priceTo' => 1000000,
                    'priceComfort' => 100000,
                    'forceShowPrice' => true,
                ]); ?>
                <?/* $this->registerJs('lsfw.pages.tourSearch.formPrix.forceShowPrice = true;') ?>
                <? $this->registerJs('lsfw.pages.tourSearch.formPrix.reloadPriceLabel();') */?>



            </div>
            <div class="tour-selection-wrap-in">

                <div class="rbt-block mt0 mb0 ">
                    <input type="radio" name="types" class="rbt " id="type1" checked="">
                    <label class=" js-type1 label-rbt" for="type1">
                        <span class="rbt-cnt uppercase">Турпакет</span>
                    </label>
                </div>

                <div class="rbt-block   mt0 mb0">
                    <input type="radio" name="types" class="rbt " id="type2">
                    <label class="js-type2 label-rbt" for="type2">
                        <span class="rbt-cnt uppercase">Конкретный отель</span>
                    </label>
                </div>


            </div>

            <div class=" js-types-search-tours-blocks">

                <? for ($i = 0; $i < 3; $i++) { ?>
                <div data-tour-row="<?=$i?>" class="tour-selection-wrap-in tour-selection-wrap-flex <? if ($i > 0) { ?>tour-selection-wrap-in--hidden<? } ?>">
                    <div class="tour-selection-field tour-selection-field--250 ">
                        <div class="bth__inp-block">
                            <span class="bth__inp-lbl active">Страна поездки</span>
                            <div class="bth__inp tour-selection__country  js-show-formDirections"></div>
                            <div class="formDirections w100p" style="display: none;">
                                <div class="formDirections__wrap w100p">
                                    <div class="formDirections__top  formDirections__top-line">
                                        <i class="formDirections__bottom-close"></i>
                                        <div class="formDirections__top-tab super-grey ">Страна поездки</div>
                                    </div>

                                    <div class="SumoSelect formDirections__SumoSelect formDirections__SumoSelect-search">
                                        <select class="sumo-direction"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tour-selection-field tour-selection-field--180">
                        <div class="bth__inp-block js-show-formDirections">
                            <span class="bth__inp-lbl ">Город</span>
                            <span class="bth__inp  uppercase "></span>
                        </div>
                        <div class="formDirections w100p" style="display: none;">
                            <div class="formDirections__wrap w100p">
                                <div class="formDirections__top  formDirections__top-line">
                                    <i class="formDirections__bottom-close"></i>
                                    <div class="formDirections__top-tab super-grey ">Страна поездки</div>
                                </div>
                                <div class="SumoSelect formDirections__SumoSelect formDirections__SumoSelect-search">
                                    <select class="sumo-direction-city"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tour-selection-field tour-selection-field--200">
                        <div class="bth__inp-block js-show-formDirections ">
                            <span class="bth__inp-lbl ">Город вылета</span>
                            <span class="bth__inp uppercase"></span>
                        </div>


                        <div class="formDirections w100p" style="display: none;">
                            <div class="formDirections__wrap w100p">

                                <div class="formDirections__top  formDirections__top-line">

                                    <i class="formDirections__bottom-close"></i>
                                    <div class="formDirections__top-tab super-grey ">Город вылета</div>
                                </div>

                                <div class="SumoSelect formDirections__SumoSelect formDirections__SumoSelect-search">
                                    <select class="sumo-department"></select>
                                </div>

                            </div>
                        </div>


                    </div>


                    <div class="tour-selection-field tour-selection-field--180">
                        <div class="bth__inp-block js-show-formDirections js-formDirections--big-mobile">
                            <span class="bth__inp-lbl ">Параметры отеля</span>
                            <span class="bth__inp"></span>
                        </div>

                        <div class="formDirections   formDirections--big-mobile formDirections--char">

                            <div class="formDirections__top  formDirections__top-line">
                                <i class="formDirections__bottom-close"></i>
                                <div class="formDirections__top-tab super-grey">Параметры отеля</div>
                            </div>


                            <div class="formDirections__wrap formDirections__row">

                                <div class="formDirections__wrap-flex">
                                    <div class="formDirections__top  formDirections__top-line">


                                        <div class="formDirections__top-tab active js-act-stars">
                                            Категория
                                        </div>

                                        <div class="formDirections__top-tab js-act-rating">

                                            Рейтинг
                                        </div>
                                        <div class="formDirections__top-tab js-act-hotels">

                                            Питание
                                        </div>
                                        <div class="formDirections__top-tab js-act-country">

                                            Расположение
                                        </div>
                                        <div class="formDirections__top-tab js-act-kid">

                                            Для детей
                                        </div>
                                        <div class="formDirections__top-tab js-act-other">

                                            Прочее
                                        </div>

                                    </div>
                                    <div class="formDirections__wrap-flex-right">
                                        <div class="formDirections__bottom js-search-country" style="display: none">

                                            <div class="formDirections__bottom-blocks">
                                                <div class="form-dropdown-stars__item">
                                                    <div class="cbx-block   cbx-block--16 ">
                                                        <input type="checkbox" class="cbx" id="catalog-positionckd"
                                                               checked>
                                                        <label class="label-cbx" for="catalog-positionckd">
                                                            <span class="cbx-cnt">Любой тип</span>
                                                        </label>

                                                    </div>
                                                </div>

                                                <div class="formDirections__cbx-ttl">Пляжный</div>
                                                <div class=" formDirections__left-30 ">
                                                    <div class="cbx-block   cbx-block--16 ">
                                                        <input type="checkbox" class="cbx" id="catalog-position1">
                                                        <label class="label-cbx" for="catalog-position1">
                                                            <span class="cbx-cnt">1-я линия от моря</span>
                                                        </label>

                                                    </div>
                                                    <div class="cbx-block  cbx-block--16   ">
                                                        <input type="checkbox" class="cbx" id="catalog-position2">
                                                        <label class="label-cbx" for="catalog-position2">
                                                            <span class="cbx-cnt">2-я линия от моря </span>
                                                        </label>

                                                    </div>
                                                    <div class="cbx-block   cbx-block--16  ">
                                                        <input type="checkbox" class="cbx" id="catalog-position3">
                                                        <label class="label-cbx" for="catalog-position3">
                                                            <span class="cbx-cnt"> 3-я линия от моря</span>
                                                        </label>

                                                    </div>
                                                    <div class="cbx-block   cbx-block--16  ">
                                                        <input type="checkbox" class="cbx" id="catalog-position4">
                                                        <label class="label-cbx" for="catalog-position4">
                                                            <span class="cbx-cnt">Через дорогу </span>
                                                        </label>

                                                    </div>
                                                </div>

                                                <div class="formDirections__cbx-ttl">Горнолыжный</div>
                                                <div class=" formDirections__left-30 ">
                                                    <div class="cbx-block   cbx-block--16  ">
                                                        <input type="checkbox" class="cbx" id="catalog-position5">
                                                        <label class="label-cbx" for="catalog-position5">
                                                            <span class="cbx-cnt">Близко</span>
                                                        </label>

                                                    </div>
                                                    <div class="cbx-block  cbx-block--16  ">
                                                        <input type="checkbox" class="cbx" id="catalog-position6">
                                                        <label class="label-cbx" for="catalog-position6">
                                                            <span class="cbx-cnt">Далеко </span>
                                                        </label>

                                                    </div>
                                                    <div class="cbx-block  cbx-block--16  ">
                                                        <input type="checkbox" class="cbx" id="catalog-position7">
                                                        <label class="label-cbx" for="catalog-position7">
                                                            <span class="cbx-cnt"> Рядом</span>
                                                        </label>

                                                    </div>
                                                </div>

                                                <div class="formDirections__cbx-ttl">Загородный</div>
                                                <div class=" formDirections__left-30 ">
                                                    <div class="cbx-block   cbx-block--16 ">
                                                        <input type="checkbox" class="cbx" id="catalog-position8">
                                                        <label class="label-cbx" for="catalog-position8">
                                                            <span class="cbx-cnt">Близко</span>
                                                        </label>

                                                    </div>
                                                    <div class="cbx-block  cbx-block--16  ">
                                                        <input type="checkbox" class="cbx" id="catalog-position9">
                                                        <label class="label-cbx" for="catalog-position9">
                                                            <span class="cbx-cnt">Далеко </span>
                                                        </label>

                                                    </div>
                                                    <div class="cbx-block  cbx-block--16  ">
                                                        <input type="checkbox" class="cbx" id="catalog-position10">
                                                        <label class="label-cbx" for="catalog-position10">
                                                            <span class="cbx-cnt"> Рядом</span>
                                                        </label>

                                                    </div>
                                                </div>

                                                <div class="formDirections__cbx-ttl">Городской</div>
                                                <div class=" formDirections__left-30 ">
                                                    <div class="cbx-block   cbx-block--16 ">
                                                        <input type="checkbox" class="cbx" id="catalog-position11">
                                                        <label class="label-cbx" for="catalog-position11">
                                                            <span class="cbx-cnt">Близко к центру</span>
                                                        </label>

                                                    </div>
                                                    <div class="cbx-block  cbx-block--16  ">
                                                        <input type="checkbox" class="cbx" id="catalog-position12">
                                                        <label class="label-cbx" for="catalog-position12">
                                                            <span class="cbx-cnt">Окраина </span>
                                                        </label>

                                                    </div>
                                                    <div class="cbx-block  cbx-block--16  ">
                                                        <input type="checkbox" class="cbx" id="catalog-position13">
                                                        <label class="label-cbx" for="catalog-position13">
                                                            <span class="cbx-cnt"> Центр</span>
                                                        </label>

                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                        <div class="formDirections__bottom js-search-hotels" style="display: none">

                                            <div class="formDirections__bottom-blocks">

                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block    cbx-block--16 ">
                                                        <input type="checkbox" class="cbx" id="333eat2-typeckd"
                                                               checked>
                                                        <label class="label-cbx" for="333eat2-typeckd">
                                                            <span class="cbx-cnt">Любое питание</span>
                                                        </label>

                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block    cbx-block--16 ">
                                                        <input type="checkbox" class="cbx" id="333eat2-type1">
                                                        <label class="label-cbx" for="333eat2-type1">
                                                            <span class="cbx-cnt">AI Все включено</span>
                                                        </label>

                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block   cbx-block--16  ">
                                                        <input type="checkbox" class="cbx" id="333eat2-type2">
                                                        <label class="label-cbx" for="333eat2-type2">
                                                            <span class="cbx-cnt">FB  Завтрак + обед + ужин</span>
                                                        </label>

                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block    cbx-block--16 ">
                                                        <input type="checkbox" class="cbx" id="333eat2-type3">
                                                        <label class="label-cbx" for="333eat2-type3">
                                                            <span class="cbx-cnt">HB  Завтрак +  ужин</span>
                                                        </label>

                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block     cbx-block--16">
                                                        <input type="checkbox" class="cbx" id="333eat2-type4">
                                                        <label class="label-cbx" for="333eat2-type4">
                                                            <span class="cbx-cnt"> BB Завтрак</span>
                                                        </label>

                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block    cbx-block--16 ">
                                                        <input type="checkbox" class="cbx" id="333eat2-type5">
                                                        <label class="label-cbx" for="333eat2-type5">
                                                            <span class="cbx-cnt">RO Без питания</span>
                                                        </label>

                                                    </div>
                                                </div>


                                            </div>

                                        </div>
                                        <div class="formDirections__bottom js-search-stars">

                                            <div class="formDirections__bottom-blocks">
                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block  cbx-block--16  ">
                                                        <input type="checkbox" class="cbx" id="333stars-ckd"
                                                               checked>
                                                        <label class="label-cbx " for="333stars-ckd">
                                                            <span class="cbx-cnt">Любая категория</span>
                                                        </label>
                                                    </div>

                                                </div>
                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block  cbx-block--16  ">
                                                        <input type="checkbox" class="cbx" id="333stars-5">
                                                        <label class="label-cbx " for="333stars-5">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </label>
                                                    </div>

                                                </div>
                                                <div class="form-dropdown-stars__item">
                                                    <div class="cbx-block    cbx-block--16">
                                                        <input type="checkbox" class="cbx" id="333stars-4">
                                                        <label class="label-cbx " for="333stars-4">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item">
                                                    <div class="cbx-block   cbx-block--16 ">
                                                        <input type="checkbox" class="cbx" id="333stars-3">
                                                        <label class="label-cbx " for="333stars-3">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item">
                                                    <div class="cbx-block   cbx-block--16 ">
                                                        <input type="checkbox" class="cbx" id="333stars-2">
                                                        <label class="label-cbx " for="333stars-2">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item">
                                                    <div class="cbx-block   cbx-block--16 ">
                                                        <input type="checkbox" class="cbx" id="333stars-1">
                                                        <label class="label-cbx " for="333stars-1">
                                                            <i class="fa fa-star"></i>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item">
                                                    <div class="cbx-block   cbx-block--16 ">
                                                        <input type="checkbox" class="cbx" id="333stars-hv1">
                                                        <label class="label-cbx" for="333stars-hv1">
                                                            <span class="cbx-cnt">HV1</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item">
                                                    <div class="cbx-block   cbx-block--16 ">
                                                        <input type="checkbox" class="cbx" id="333stars-hv2">
                                                        <label class="label-cbx" for="333stars-hv2">
                                                            <span class="cbx-cnt">HV2</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item">
                                                    <div class="cbx-block    cbx-block--16">
                                                        <input type="checkbox" class="cbx" id="no-stars">
                                                        <label class="label-cbx" for="no-stars">
                                                            <span class="cbx-cnt">Без категории</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="formDirections__bottom-blocks js-search-rating"
                                             style="display: none">


                                            <div class="form-dropdown-stars__item ">
                                                <div class="rbt-block  ">
                                                    <input type="radio" name="333rating" class="rbt "
                                                           id="333ratingckd" checked>
                                                    <label class="label-rbt" for="333ratingckd">
                                                        <span class="rbt-cnt  uppercase">Любой рейтинг</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-dropdown-stars__item ">
                                                <div class="rbt-block  ">
                                                    <input type="radio" name="333rating" class="rbt "
                                                           id="333rating1">
                                                    <label class="label-rbt" for="333rating1">
                                                        <span class="rbt-cnt  uppercase">Не важно</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-dropdown-stars__item ">
                                                <div class="rbt-block  ">
                                                    <input type="radio" name="333rating" class="rbt "
                                                           id="333rating3">
                                                    <label class="label-rbt" for="333rating3">
                                                        <span class="rbt-cnt  uppercase"> Не ниже 4,75</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-dropdown-stars__item ">
                                                <div class="rbt-block  ">
                                                    <input type="radio" name="333rating" class="rbt "
                                                           id="333rating4">
                                                    <label class="label-rbt" for="333rating4">
                                                        <span class="rbt-cnt  uppercase">  Не ниже 4,5</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="form-dropdown-stars__item ">
                                                <div class="rbt-block  ">
                                                    <input type="radio" name="333rating" class="rbt "
                                                           id="333rating5">
                                                    <label class="label-rbt" for="333rating5">
                                                        <span class="rbt-cnt  uppercase">  Не ниже 4,25</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-dropdown-stars__item ">
                                                <div class="rbt-block  ">
                                                    <input type="radio" name="333rating" class="rbt "
                                                           id="333rating6">
                                                    <label class="label-rbt" for="333rating6">
                                                        <span class="rbt-cnt  uppercase">Не ниже 4,0</span>
                                                    </label>
                                                </div>
                                            </div>


                                            <div class="form-dropdown-stars__item ">
                                                <div class="rbt-block  ">
                                                    <input type="radio" name="333rating" class="rbt "
                                                           id="333rating7">
                                                    <label class="label-rbt" for="333rating7">
                                                        <span class="rbt-cnt  uppercase">Не ниже 3,75</span>
                                                    </label>
                                                </div>
                                            </div>


                                            <div class="form-dropdown-stars__item ">
                                                <div class="rbt-block  ">
                                                    <input type="radio" name="333rating" class="rbt "
                                                           id="333rating8">
                                                    <label class="label-rbt" for="333rating8">
                                                        <span class="rbt-cnt  uppercase">     Не ниже 3,5</span>
                                                    </label>
                                                </div>
                                            </div>


                                            <div class="form-dropdown-stars__item ">
                                                <div class="rbt-block  ">
                                                    <input type="radio" name="333rating" class="rbt "
                                                           id="333rating9">
                                                    <label class="label-rbt" for="333rating9">
                                                        <span class="rbt-cnt  uppercase">       Не ниже 3,25</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="formDirections__bottom js-search-kid" style="display: none">

                                            <div class="formDirections__bottom-blocks">

                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block   cbx-block--16  ">
                                                        <input type="checkbox" class="cbx" id="333kid1">
                                                        <label class="label-cbx" for="333kid1">
                                                            <span class="cbx-cnt">ДЕТСКИЙ ГОРШОК</span>
                                                        </label>

                                                    </div>
                                                </div>


                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block    cbx-block--16 ">
                                                        <input type="checkbox" class="cbx" id="333kid2">
                                                        <label class="label-cbx" for="333kid2">
                                                            <span class="cbx-cnt">  ДЕТСКИЕ БЛЮДА</span>
                                                        </label>

                                                    </div>
                                                </div>


                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block   cbx-block--16  ">
                                                        <input type="checkbox" class="cbx" id="333kid3">
                                                        <label class="label-cbx" for="333kid3">
                                                            <span class="cbx-cnt">ПЕЛЕНАЛЬНЫЙ СТОЛИК</span>
                                                        </label>

                                                    </div>
                                                </div>

                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block   cbx-block--16  ">
                                                        <input type="checkbox" class="cbx" id="333kid4">
                                                        <label class="label-cbx" for="333kid4">
                                                            <span class="cbx-cnt">AНИМАЦИЯ</span>
                                                        </label>

                                                    </div>
                                                </div>


                                            </div>

                                        </div>
                                        <div class="formDirections__bottom js-search-other" style="display: none">

                                            <div class="formDirections__bottom-blocks">

                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block   cbx-block--16  ">
                                                        <input type="checkbox" class="cbx" id="333other1">
                                                        <label class="label-cbx" for="333other1">
                                                            <span class="cbx-cnt">ВЕСЕЛАЯ АНИМАЦИЯ</span>
                                                        </label>

                                                    </div>
                                                </div>


                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block    cbx-block--16 ">
                                                        <input type="checkbox" class="cbx" id="333other2">
                                                        <label class="label-cbx" for="333other2">
                                                            <span class="cbx-cnt">  ТУСОВКИ РЯДОМ С ОТЕЛЕМ </span>
                                                        </label>

                                                    </div>
                                                </div>


                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="formDirections__btn-orange js-close-formDirections">Применить</div>
                        </div>


                    </div>
                    <? if ($i == 0) { ?>
                        <span class=" tour-selection-plus  hide-1023 js-add-field">
                            <i class="fas fa-plus"></i>
                        </span>
                    <? } else { ?>
                        <span class=" tour-selection-plus js-del-field">
                            <i class="fas fa-minus"></i>
                        </span>
                    <? } ?>
                </div>

                <? } ?>


            </div>


            <div class=" js-types-search-hotel-blocks" style="display: none">
                <div class="tour-selection-wrap-in tour-selection-wrap-flex ">
                    <div class="tour-selection-field tour-selection-field--250">
                        <div class="bth__inp-block js-show-formDirections">
                            <span class="bth__inp-lbl ">Город вылета</span>
                            <span class="bth__inp">
                                </span>
                        </div>

                        <div class="formDirections w100p" style="display: none;">
                            <div class="formDirections__wrap w100p">

                                <div class="formDirections__top  formDirections__top-line">

                                    <i class="formDirections__bottom-close"></i>
                                    <div class="formDirections__top-tab super-grey ">Город вылета</div>
                                </div>

                                <div class="SumoSelect formDirections__SumoSelect formDirections__SumoSelect-search">
                                    <select id="sumo-department">

                                        <option>Москва</option>
                                        <option>Санкт-Петербург</option>
                                        <option>Абакан</option>
                                        <option>Агзу</option>
                                        <option>Абакан</option>
                                        <option>Агзу</option>
                                        <option>Абакан</option>
                                        <option>Агзу</option>
                                        <option>Абакан</option>
                                        <option>Агзу</option>
                                        <option>Москва</option>
                                        <option>Санкт-Петербург</option>
                                        <option>Абакан</option>
                                        <option>Агзу</option>
                                        <option>Абакан</option>
                                        <option>Агзу</option>
                                        <option>Абакан</option>
                                        <option>Агзу</option>
                                        <option>Абакан</option>
                                        <option>Агзу</option>

                                    </select>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="tour-selection-field tour-selection-field--250">
                        <div class="bth__inp-block js-show-formDirections">

                            <span class="bth__inp-lbl ">Питание</span>
                            <span class="bth__inp">
                                </span>
                        </div>
                        <div class="formDirections">

                            <div class="formDirections__top  formDirections__top-line">
                                <i class="formDirections__bottom-close"></i>
                                <div class="formDirections__top-tab super-grey">
                                    Питание
                                </div>
                            </div>


                            <div class="formDirections__wrap">

                                <div class="formDirections__bottom ">

                                    <div class="formDirections__bottom-blocks">

                                        <div class="form-dropdown-stars__item ">
                                            <div class="cbx-block    cbx-block--16 ">
                                                <input type="checkbox" class="cbx" id="8eat2-type1">
                                                <label class="label-cbx" for="8eat2-type1">
                                                    <span class="cbx-cnt">AI Все включено</span>
                                                </label>

                                            </div>
                                        </div>
                                        <div class="form-dropdown-stars__item ">
                                            <div class="cbx-block    cbx-block--16">
                                                <input type="checkbox" class="cbx" id="8eat2-type2">
                                                <label class="label-cbx" for="8eat2-type2">
                                                    <span class="cbx-cnt">FB  Завтрак + обед + ужин</span>
                                                </label>

                                            </div>
                                        </div>
                                        <div class="form-dropdown-stars__item ">
                                            <div class="cbx-block    cbx-block--16 ">
                                                <input type="checkbox" class="cbx" id="8eat2-type3">
                                                <label class="label-cbx" for="8eat2-type3">
                                                    <span class="cbx-cnt">HB  Завтрак +  ужин</span>
                                                </label>

                                            </div>
                                        </div>
                                        <div class="form-dropdown-stars__item ">
                                            <div class="cbx-block    cbx-block--16 ">
                                                <input type="checkbox" class="cbx" id="8eat2-type4">
                                                <label class="label-cbx" for="8eat2-type4">
                                                    <span class="cbx-cnt"> BB Завтрак</span>
                                                </label>

                                            </div>
                                        </div>
                                        <div class="form-dropdown-stars__item ">
                                            <div class="cbx-block   cbx-block--16  ">
                                                <input type="checkbox" class="cbx" id="8eat2-type5">
                                                <label class="label-cbx" for="8eat2-type5">
                                                    <span class="cbx-cnt">RO Без питания</span>
                                                </label>

                                            </div>
                                        </div>
                                        <div class="formDirections__static-btn js-close-formDirections">Применить
                                        </div>


                                    </div>

                                </div>


                            </div>
                        </div>

                    </div>
                </div>
                <div class="tour-selection-wrap-in tour-selection-wrap-flex ">
                    <div class="tour-selection-field tour-selection-field--740">
                        <div class="bth__inp-block js-show-formDirections js-formDirections--big-mobile">

                            <span class="bth__inp-lbl ">Добавить отель</span>
                            <span class="bth__inp"></span>
                        </div>

                        <div class="formDirections formDirections--big-mobile w100p">
                            <div class="formDirections__wrap w100p">
                                <div class="formDirections__top formDirections__top--white">

                                    <i class="formDirections__bottom-close"></i>
                                    <div class="formDirections__top-tab super-grey">
                                        Добавить отель
                                    </div>
                                </div>


                                <div class="formDirections__bottom">

                                    <div class="formDirections__search">
                                        <input class="bth__inp" type="text" placeholder="Поиск отеля">
                                    </div>
                                    <div class="formDirections__wrap  formDirections__bottom-blocks-cut">

                                        <div class="formDirections__bottom-item">
                                            <div class="formDirections__city">
                                                <div class=" lsfw-flag lsfw-flag--30w lsfw-flag-1">
                                                    <div class="hint">Россия</div>
                                                </div>
                                                <span class="formDirections__cut"> Mriya Resort &amp; Spa (Мрия Резорт энд Спа) </span>5*
                                            </div>
                                            <span class="formDirections__count">Агитос Антониос</span>
                                        </div>
                                        <div class="formDirections__bottom-item">
                                            <div class="formDirections__city">
                                                <div class=" lsfw-flag lsfw-flag--30w lsfw-flag-1">
                                                    <div class="hint">Россия</div>
                                                </div>
                                                <span class="formDirections__cut"> Resort &amp; Spa</span> 5*
                                            </div>
                                            <span class="formDirections__count">Кампос</span>
                                        </div>
                                        <div class="formDirections__bottom-item">
                                            <div class="formDirections__city">
                                                <div class=" lsfw-flag lsfw-flag--30w lsfw-flag-1">
                                                    <div class="hint">Россия</div>
                                                </div>
                                                <span class="formDirections__cut"> Resort &amp; Spa Mriya </span>5*
                                            </div>
                                            <span class="formDirections__count">Каравостаси</span>
                                        </div>
                                        <div class="formDirections__bottom-item">
                                            <div class="formDirections__city">
                                                <div class=" lsfw-flag lsfw-flag--30w lsfw-flag-1">
                                                    <div class="hint">Россия</div>
                                                </div>

                                                <span class="formDirections__cut"> Resort &amp; Spa Mriya</span> 5*

                                            </div>
                                            <span class="formDirections__count">Никитари</span>
                                        </div>
                                        <div class="formDirections__bottom-item">
                                            <div class="formDirections__city">
                                                <div class=" lsfw-flag lsfw-flag--30w lsfw-flag-1">
                                                    <div class="hint">Россия</div>
                                                </div>
                                                <span class="formDirections__cut"> Mriya Resort &amp; Spa (Мрия Резорт энд Спа) </span>5*
                                            </div>
                                            <span class="formDirections__count">Агитос Антониос</span>
                                        </div>
                                        <div class="formDirections__bottom-item">
                                            <div class="formDirections__city">
                                                <div class=" lsfw-flag lsfw-flag--30w lsfw-flag-1">
                                                    <div class="hint">Россия</div>
                                                </div>
                                                <span class="formDirections__cut"> Resort &amp; Spa</span> 5*
                                            </div>
                                            <span class="formDirections__count">Кампос</span>
                                        </div>
                                        <div class="formDirections__bottom-item">
                                            <div class="formDirections__city">
                                                <div class=" lsfw-flag lsfw-flag--30w lsfw-flag-1">
                                                    <div class="hint">Россия</div>
                                                </div>
                                                <span class="formDirections__cut"> Resort &amp; Spa Mriya </span>5*
                                            </div>
                                            <span class="formDirections__count">Каравостаси</span>
                                        </div>
                                        <div class="formDirections__bottom-item">
                                            <div class="formDirections__city">
                                                <div class=" lsfw-flag lsfw-flag--30w lsfw-flag-1">
                                                    <div class="hint">Россия</div>
                                                </div>

                                                <span class="formDirections__cut"> Resort &amp; Spa Mriya</span> 5*

                                            </div>
                                            <span class="formDirections__count">Никитари</span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <span class="tour-selection-plus hide-1023 js-add-hotel"><i class="fas fa-plus"></i></span>
                </div>

                <div class="tour-selection-wrap-in tour-selection-wrap-flex js-show-add-hotel "
                     style="display: none">
                    <div class="tour-selection-field tour-selection-field--740">
                        <div class="bth__inp-block js-show-formDirections js-formDirections--big-mobile">

                            <span class="bth__inp-lbl ">Добавить отель</span>
                            <span class="bth__inp"></span>
                        </div>
                        <div class="formDirections formDirections--big-mobile w100p">
                            <div class="formDirections__wrap w100p">
                                <div class="formDirections__top formDirections__top--white">

                                    <i class="formDirections__bottom-close"></i>
                                    <div class="formDirections__top-tab super-grey">
                                        Добавить отель
                                    </div>
                                </div>


                                <div class="formDirections__bottom">

                                    <div class="formDirections__search">
                                        <input class="bth__inp" type="text" placeholder="Поиск отеля">
                                    </div>
                                    <div class="formDirections__wrap  formDirections__bottom-blocks-cut">

                                        <div class="formDirections__bottom-item">
                                            <div class="formDirections__city">
                                                <div class=" lsfw-flag lsfw-flag--30w lsfw-flag-1">
                                                    <div class="hint">Россия</div>
                                                </div>
                                                <span class="formDirections__cut"> Mriya Resort &amp; Spa (Мрия Резорт энд Спа) </span>5*
                                            </div>
                                            <span class="formDirections__count">Агитос Антониос</span>
                                        </div>
                                        <div class="formDirections__bottom-item">
                                            <div class="formDirections__city">
                                                <div class=" lsfw-flag lsfw-flag--30w lsfw-flag-1">
                                                    <div class="hint">Россия</div>
                                                </div>
                                                <span class="formDirections__cut"> Resort &amp; Spa</span> 5*
                                            </div>
                                            <span class="formDirections__count">Кампос</span>
                                        </div>
                                        <div class="formDirections__bottom-item">
                                            <div class="formDirections__city">
                                                <div class=" lsfw-flag lsfw-flag--30w lsfw-flag-1">
                                                    <div class="hint">Россия</div>
                                                </div>
                                                <span class="formDirections__cut"> Resort &amp; Spa Mriya </span>5*
                                            </div>
                                            <span class="formDirections__count">Каравостаси</span>
                                        </div>
                                        <div class="formDirections__bottom-item">
                                            <div class="formDirections__city">
                                                <div class=" lsfw-flag lsfw-flag--30w lsfw-flag-1">
                                                    <div class="hint">Россия</div>
                                                </div>

                                                <span class="formDirections__cut"> Resort &amp; Spa Mriya</span> 5*

                                            </div>
                                            <span class="formDirections__count">Никитари</span>
                                        </div>
                                        <div class="formDirections__bottom-item">
                                            <div class="formDirections__city">
                                                <div class=" lsfw-flag lsfw-flag--30w lsfw-flag-1">
                                                    <div class="hint">Россия</div>
                                                </div>
                                                <span class="formDirections__cut"> Mriya Resort &amp; Spa (Мрия Резорт энд Спа) </span>5*
                                            </div>
                                            <span class="formDirections__count">Агитос Антониос</span>
                                        </div>
                                        <div class="formDirections__bottom-item">
                                            <div class="formDirections__city">
                                                <div class=" lsfw-flag lsfw-flag--30w lsfw-flag-1">
                                                    <div class="hint">Россия</div>
                                                </div>
                                                <span class="formDirections__cut"> Resort &amp; Spa</span> 5*
                                            </div>
                                            <span class="formDirections__count">Кампос</span>
                                        </div>
                                        <div class="formDirections__bottom-item">
                                            <div class="formDirections__city">
                                                <div class=" lsfw-flag lsfw-flag--30w lsfw-flag-1">
                                                    <div class="hint">Россия</div>
                                                </div>
                                                <span class="formDirections__cut"> Resort &amp; Spa Mriya </span>5*
                                            </div>
                                            <span class="formDirections__count">Каравостаси</span>
                                        </div>
                                        <div class="formDirections__bottom-item">
                                            <div class="formDirections__city">
                                                <div class=" lsfw-flag lsfw-flag--30w lsfw-flag-1">
                                                    <div class="hint">Россия</div>
                                                </div>

                                                <span class="formDirections__cut"> Resort &amp; Spa Mriya</span> 5*

                                            </div>
                                            <span class="formDirections__count">Никитари</span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                    <span class=" tour-selection-plus  js-del-hotel"><i class="fas fa-minus"></i></span>
                </div>


            </div>


            <div class="tour-selection-wrap-in">
                <div class="bth__ta-resizable-wrap">
                    <div class="bth__ta-resizable" contenteditable=""></div>

                    <span class="bth__ta-resizable-hint">Дополнительные пожелания</span>

                </div>
            </div>
            <div class="tour-selection-wrap-in">
                <div class=" bth__btn  bth__btn--fill bth__loader">
                    Сформировать заявку
                    <div class=" bth__loader-spin">
                        <i class="fas fa-circle"></i>
                        <i class="fas fa-circle"></i>
                        <i class="fas fa-circle"></i>
                    </div>
                </div>


            </div>
        </div>
        <?php ActiveForm::end() ?>
    </div>


   <div class="panel form-panel" id="formPanel" >

        <div class="form-panel__success">
            <div class="bth__cnt fz18 bold">Спасибо, Ваша заявка отправлена и будет обработана в ближайшее время.</div>
        </div>

        <div class="form-panel__wrapper">
            <div class="bth__cnt uppercase">Пожалуйста, укажите параметры вашей поездки</div>



            <div class="tour-selection-wrap">


                <div class="tour-selection-wrap-in">

                    <div class="bth__inp-block long">

                        <textarea type="text" class="js-add-error bth__inp  bold js-stop-label" id="parametrs"></textarea>
                        <label for="parametrs" class="bth__inp-lbl">
                            <span class="block  mb5">- укажите страну, курорт или отель</span>
                            <span class="block  mb5">- количество человек</span>
                            <span class="block  mb5">- ваши предпочтения по отелю</span>
                            <span class="block mb5">- ваш бюджет</span>
                            <span class="block">- другие пожелания</span>
                        </label>
                        <div class="hint-block hint-block--abs">
                            <i class="fa fa-question-circle question-error" aria-hidden="true"></i>
                            <div class="hint">
                                <p class="bth__cnt">Поле не должно быть пустым</p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="tour-selection-wrap-in tour-selection-wrap-flex">

                    <div class="tour-selection-field tour-selection-field--30p">
                        <div class="js-add-error bth__inp-block  ">
                            <input type="text" class="bth__inp js-label" id="name1">
                            <label for="name1" class="bth__inp-lbl">Ваше имя</label>
                            <div class="hint-block hint-block--abs">
                                <i class="fa fa-question-circle question-error" aria-hidden="true"></i>
                                <div class="hint">
                                    <p class="bth__cnt">Поле не должно быть пустым</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tour-selection-field tour-selection-field--30p">

                        <div class="js-add-error bth__inp-block ">
                            <input type="text" class="bth__inp js-label" id="phone1" placeholder="">
                            <label for="phone1" class="bth__inp-lbl">Телефон</label>
                            <div class="hint-block hint-block--abs">
                                <i class="fa fa-question-circle question-error" aria-hidden="true"></i>
                                <div class="hint">
                                    <p class="bth__cnt">Поле не должно быть пустым</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tour-selection-field tour-selection-field--30p">

                        <div class="bth__inp-block  ">
                            <input type="text" class="bth__inp js-label " id="mail3">
                            <label for="mail3" class="bth__inp-lbl">Email (не обязательно)</label>
                            <div class="hint-block hint-block--abs">
                                <i class="fa fa-question-circle question-error" aria-hidden="true"></i>
                                <div class="hint">
                                    <p class="bth__cnt">Поле не должно быть пустым</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="tour-selection-wrap-in">
                    <div class=" bth__btn  bth__btn--fill btn-custom-order">
                        Отправить заявку*
                        <div class=" bth__loader-spin">
                            <i class="fas fa-circle"></i>
                            <i class="fas fa-circle"></i>
                            <i class="fas fa-circle"></i>
                        </div>
                    </div>

                    <div class="tour-selection-wrap__abs-txt  bth__cnt bth__cnt--sm">
                        *Нажимая на кнопку "отправить", я принимаю
                        <a href="#p-agreement-pp" class="p-agreement-pp agree">
                            Соглашение об обработке личных данных</a> и
                        <a href="#p-agreement-pp" class="p-agreement-pp site-role">Правила сайта</a>
                    </div>

                </div>

            </div>
        </div>


    </div>


</div>


<?php
$js = <<<JS
$(document).ready(function() {
    
});



JS;

$this->registerJs($js);
?>