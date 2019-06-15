<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'TopHotels';
?>
<div class="tabs-block">
    <div class="tabs-bar   tabs-bar--responsive js-768-tabs">
        <div id="step1" class="tab active">Подбор тура</div>
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
                    <input type="radio" name="types" class="rbt " id="type1" checked="" value="tours">
                    <label class=" js-type1 label-rbt" for="type1">
                        <span class="rbt-cnt uppercase">Турпакет</span>
                    </label>
                </div>

                <div class="rbt-block   mt0 mb0">
                    <input type="radio" name="types" class="rbt " id="type2" value="hotel">
                    <label class="js-type2 label-rbt" for="type2">
                        <span class="rbt-cnt uppercase">Конкретный отель</span>
                    </label>
                </div>


            </div>

            <div class=" js-types-search-tours-blocks">

                <? for ($i = 0; $i < 3; $i++) { ?>
                <div data-tour-row="<?=$i?>" class="tour-selection-wrap-in tour-selection-wrap-flex <? if ($i > 0) { ?>tour-selection-wrap-in--hidden<? } ?>">
                    <div class="tour-selection-field tour-selection-field--250 ">
                        <div class="bth__inp-block bth__inp-block--direction">
                            <span class="bth__inp-lbl">Страна поездки</span>
                            <div class="tour-selection__flag lsfw-flag lsfw-flag-30"></div>
                            <div class="bth__inp js-show-formDirections uppercase"></div>
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
                        <div class="bth__inp-block bth__inp-block--direction-city js-show-formDirections">
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
                        <div class="bth__inp-block bth__inp-block--department js-show-formDirections ">
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
                        <div class="bth__inp-block bth__inp-block--hotel-params js-show-formDirections js-formDirections--big-mobile">
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
                                                        <input name="tour_place_<?=$i?>[]" value="any" type="checkbox" class="cbx" id="catalog-positionckd_<?=$i?>"
                                                               checked>
                                                        <label class="label-cbx" for="catalog-positionckd_<?=$i?>">
                                                            <span class="cbx-cnt">Любой тип</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="formDirections__cbx-ttl">Пляжный</div>
                                                <div class=" formDirections__left-30 ">
                                                    <div class="cbx-block   cbx-block--16 ">
                                                        <input name="tour_place_<?=$i?>[]" value="1_1" type="checkbox" class="cbx" id="catalog-position1_<?=$i?>">
                                                        <label class="label-cbx" for="catalog-position1_<?=$i?>">
                                                            <span class="cbx-cnt">1-я линия от моря</span>
                                                        </label>
                                                    </div>
                                                    <div class="cbx-block  cbx-block--16   ">
                                                        <input name="tour_place_<?=$i?>[]" value="1_3" type="checkbox" class="cbx" id="catalog-position2_<?=$i?>">
                                                        <label class="label-cbx" for="catalog-position2_<?=$i?>">
                                                            <span class="cbx-cnt">2-я линия от моря </span>
                                                        </label>
                                                    </div>
                                                    <div class="cbx-block   cbx-block--16  ">
                                                        <input name="tour_place_<?=$i?>[]" value="1_4" type="checkbox" class="cbx" id="catalog-position3_<?=$i?>">
                                                        <label class="label-cbx" for="catalog-position3_<?=$i?>">
                                                            <span class="cbx-cnt"> 3-я линия от моря</span>
                                                        </label>
                                                    </div>
                                                    <div class="cbx-block   cbx-block--16  ">
                                                        <input name="tour_place_<?=$i?>[]" value="1_2" type="checkbox" class="cbx" id="catalog-position4_<?=$i?>">
                                                        <label class="label-cbx" for="catalog-position4_<?=$i?>">
                                                            <span class="cbx-cnt">Через дорогу </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="formDirections__cbx-ttl">Горнолыжный</div>
                                                <div class=" formDirections__left-30 ">
                                                    <div class="cbx-block   cbx-block--16  ">
                                                        <input name="tour_place_<?=$i?>[]" value="3_9" type="checkbox" class="cbx" id="catalog-position5_<?=$i?>">
                                                        <label class="label-cbx" for="catalog-position5_<?=$i?>">
                                                            <span class="cbx-cnt">Близко</span>
                                                        </label>
                                                    </div>
                                                    <div class="cbx-block  cbx-block--16  ">
                                                        <input name="tour_place_<?=$i?>[]" value="3_10" type="checkbox" class="cbx" id="catalog-position6_<?=$i?>">
                                                        <label class="label-cbx" for="catalog-position6_<?=$i?>">
                                                            <span class="cbx-cnt">Далеко </span>
                                                        </label>
                                                    </div>
                                                    <div class="cbx-block  cbx-block--16  ">
                                                        <input name="tour_place_<?=$i?>[]" value="3_8" type="checkbox" class="cbx" id="catalog-position7_<?=$i?>">
                                                        <label class="label-cbx" for="catalog-position7_<?=$i?>">
                                                            <span class="cbx-cnt"> Рядом</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="formDirections__cbx-ttl">Загородный</div>
                                                <div class=" formDirections__left-30 ">
                                                    <div class="cbx-block   cbx-block--16 ">
                                                        <input name="tour_place_<?=$i?>[]" value="4_12" type="checkbox" class="cbx" id="catalog-position8_<?=$i?>">
                                                        <label class="label-cbx" for="catalog-position8_<?=$i?>">
                                                            <span class="cbx-cnt">Близко</span>
                                                        </label>
                                                    </div>
                                                    <div class="cbx-block  cbx-block--16  ">
                                                        <input name="tour_place_<?=$i?>[]" value="4_13" type="checkbox" class="cbx" id="catalog-position9_<?=$i?>">
                                                        <label class="label-cbx" for="catalog-position9_<?=$i?>">
                                                            <span class="cbx-cnt">Далеко </span>
                                                        </label>
                                                    </div>
                                                    <div class="cbx-block  cbx-block--16  ">
                                                        <input name="tour_place_<?=$i?>[]" value="4_11" type="checkbox" class="cbx" id="catalog-position10_<?=$i?>">
                                                        <label class="label-cbx" for="catalog-position10_<?=$i?>">
                                                            <span class="cbx-cnt"> Рядом</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="formDirections__cbx-ttl">Городской</div>
                                                <div class=" formDirections__left-30 ">
                                                    <div class="cbx-block   cbx-block--16 ">
                                                        <input name="tour_place_<?=$i?>[]" value="2_6" type="checkbox" class="cbx" id="catalog-position11_<?=$i?>">
                                                        <label class="label-cbx" for="catalog-position11_<?=$i?>">
                                                            <span class="cbx-cnt">Близко к центру</span>
                                                        </label>
                                                    </div>
                                                    <div class="cbx-block  cbx-block--16  ">
                                                        <input name="tour_place_<?=$i?>[]" value="2_7" type="checkbox" class="cbx" id="catalog-position12_<?=$i?>">
                                                        <label class="label-cbx" for="catalog-position12_<?=$i?>">
                                                            <span class="cbx-cnt">Окраина </span>
                                                        </label>
                                                    </div>
                                                    <div class="cbx-block  cbx-block--16  ">
                                                        <input name="tour_place_<?=$i?>[]" value="2_5" type="checkbox" class="cbx" id="catalog-position13_<?=$i?>">
                                                        <label class="label-cbx" for="catalog-position13_<?=$i?>">
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
                                                        <input name="tour_meal_<?=$i?>[]" value="any" type="checkbox" class="cbx" id="333eat2-typeckd_<?=$i?>"
                                                               checked>
                                                        <label class="label-cbx" for="333eat2-typeckd_<?=$i?>">
                                                            <span class="cbx-cnt">Любое питание</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block    cbx-block--16 ">
                                                        <input name="tour_meal_<?=$i?>[]" value="AI" type="checkbox" class="cbx" id="333eat2-type1_<?=$i?>">
                                                        <label class="label-cbx" for="333eat2-type1_<?=$i?>">
                                                            <span class="cbx-cnt">AI Все включено</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block   cbx-block--16  ">
                                                        <input name="tour_meal_<?=$i?>[]" value="FB" type="checkbox" class="cbx" id="333eat2-type2_<?=$i?>">
                                                        <label class="label-cbx" for="333eat2-type2_<?=$i?>">
                                                            <span class="cbx-cnt">FB  Завтрак + обед + ужин</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block    cbx-block--16 ">
                                                        <input name="tour_meal_<?=$i?>[]" value="HB" type="checkbox" class="cbx" id="333eat2-type3_<?=$i?>">
                                                        <label class="label-cbx" for="333eat2-type3_<?=$i?>">
                                                            <span class="cbx-cnt">HB  Завтрак +  ужин</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block     cbx-block--16">
                                                        <input name="tour_meal_<?=$i?>[]" value="BB" type="checkbox" class="cbx" id="333eat2-type4_<?=$i?>">
                                                        <label class="label-cbx" for="333eat2-type4_<?=$i?>">
                                                            <span class="cbx-cnt"> BB Завтрак</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block    cbx-block--16 ">
                                                        <input name="tour_meal_<?=$i?>[]" value="RO" type="checkbox" class="cbx" id="333eat2-type5_<?=$i?>">
                                                        <label class="label-cbx" for="333eat2-type5_<?=$i?>">
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
                                                        <input name="tour_category_<?=$i?>[]" value="any" type="checkbox" class="cbx" id="333stars-ckd_<?=$i?>"
                                                               checked>
                                                        <label class="label-cbx " for="333stars-ckd_<?=$i?>">
                                                            <span class="cbx-cnt">Любая категория</span>
                                                        </label>
                                                    </div>

                                                </div>
                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block  cbx-block--16  ">
                                                        <input name="tour_category_<?=$i?>[]" value="10" type="checkbox" class="cbx" id="333stars-5_<?=$i?>">
                                                        <label class="label-cbx " for="333stars-5_<?=$i?>">
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
                                                        <input name="tour_category_<?=$i?>[]" value="9" type="checkbox" class="cbx" id="333stars-4_<?=$i?>">
                                                        <label class="label-cbx " for="333stars-4_<?=$i?>">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item">
                                                    <div class="cbx-block   cbx-block--16 ">
                                                        <input name="tour_category_<?=$i?>[]" value="8" type="checkbox" class="cbx" id="333stars-3_<?=$i?>">
                                                        <label class="label-cbx " for="333stars-3_<?=$i?>">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item">
                                                    <div class="cbx-block   cbx-block--16 ">
                                                        <input name="tour_category_<?=$i?>[]" value="7" type="checkbox" class="cbx" id="333stars-2_<?=$i?>">
                                                        <label class="label-cbx " for="333stars-2_<?=$i?>">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item">
                                                    <div class="cbx-block   cbx-block--16 ">
                                                        <input name="tour_category_<?=$i?>[]" value="6" type="checkbox" class="cbx" id="333stars-1_<?=$i?>">
                                                        <label class="label-cbx " for="333stars-1_<?=$i?>">
                                                            <i class="fa fa-star"></i>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item">
                                                    <div class="cbx-block   cbx-block--16 ">
                                                        <input name="tour_category_<?=$i?>[]" value="11" type="checkbox" class="cbx" id="333stars-hv1_<?=$i?>">
                                                        <label class="label-cbx" for="333stars-hv1_<?=$i?>">
                                                            <span class="cbx-cnt">HV1</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item">
                                                    <div class="cbx-block   cbx-block--16 ">
                                                        <input name="tour_category_<?=$i?>[]" value="19" type="checkbox" class="cbx" id="333stars-hv2_<?=$i?>">
                                                        <label class="label-cbx" for="333stars-hv2_<?=$i?>">
                                                            <span class="cbx-cnt">HV2</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item">
                                                    <div class="cbx-block    cbx-block--16">
                                                        <input name="tour_category_<?=$i?>[]" value="66" type="checkbox" class="cbx" id="no-stars_<?=$i?>">
                                                        <label class="label-cbx" for="no-stars_<?=$i?>">
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
                                                    <input name="tour_rating_<?=$i?>[]" value="not_important" type="radio" class="rbt "
                                                           id="333rating1_<?=$i?>" checked="" />
                                                    <label class="label-rbt" for="333rating1_<?=$i?>">
                                                        <span class="rbt-cnt  uppercase">Не важно</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-dropdown-stars__item ">
                                                <div class="rbt-block  ">
                                                    <input name="tour_rating_<?=$i?>[]" value="4.75"  type="radio" class="rbt "
                                                           id="333rating3_<?=$i?>">
                                                    <label class="label-rbt" for="333rating3_<?=$i?>">
                                                        <span class="rbt-cnt  uppercase"> Не ниже 4,75</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-dropdown-stars__item ">
                                                <div class="rbt-block  ">
                                                    <input name="tour_rating_<?=$i?>[]" value="4.5" type="radio" class="rbt "
                                                           id="333rating4_<?=$i?>">
                                                    <label class="label-rbt" for="333rating4_<?=$i?>">
                                                        <span class="rbt-cnt  uppercase">  Не ниже 4,5</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="form-dropdown-stars__item ">
                                                <div class="rbt-block  ">
                                                    <input name="tour_rating_<?=$i?>[]" value="4.25" type="radio" class="rbt "
                                                           id="333rating5_<?=$i?>">
                                                    <label class="label-rbt" for="333rating5_<?=$i?>">
                                                        <span class="rbt-cnt  uppercase">  Не ниже 4,25</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-dropdown-stars__item ">
                                                <div class="rbt-block  ">
                                                    <input name="tour_rating_<?=$i?>[]" value="4.0" type="radio" name="333rating" class="rbt "
                                                           id="333rating6_<?=$i?>">
                                                    <label class="label-rbt" for="333rating6_<?=$i?>">
                                                        <span class="rbt-cnt  uppercase">Не ниже 4,0</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-dropdown-stars__item ">
                                                <div class="rbt-block  ">
                                                    <input name="tour_rating_<?=$i?>[]" value="3.75" type="radio" class="rbt "
                                                           id="333rating7_<?=$i?>">
                                                    <label class="label-rbt" for="333rating7_<?=$i?>">
                                                        <span class="rbt-cnt  uppercase">Не ниже 3,75</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-dropdown-stars__item ">
                                                <div class="rbt-block  ">
                                                    <input name="tour_rating_<?=$i?>[]" value="3.5" type="radio" class="rbt "
                                                           id="333rating8_<?=$i?>">
                                                    <label class="label-rbt" for="333rating8_<?=$i?>">
                                                        <span class="rbt-cnt  uppercase">     Не ниже 3,5</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-dropdown-stars__item ">
                                                <div class="rbt-block  ">
                                                    <input name="tour_rating_<?=$i?>[]" value="3.25" type="radio" class="rbt "
                                                           id="333rating9_<?=$i?>">
                                                    <label class="label-rbt" for="333rating9_<?=$i?>">
                                                        <span class="rbt-cnt  uppercase">       Не ниже 3,25</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="formDirections__bottom js-search-kid" style="display: none">
                                            <div class="formDirections__bottom-blocks">
                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block   cbx-block--16  ">
                                                        <input name="tour_baby_<?=$i?>[]" value="potty" type="checkbox" class="cbx" id="333kid1_<?=$i?>">
                                                        <label class="label-cbx" for="333kid1_<?=$i?>">
                                                            <span class="cbx-cnt">ДЕТСКИЙ ГОРШОК</span>
                                                        </label>

                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block    cbx-block--16 ">
                                                        <input name="tour_baby_<?=$i?>[]" value="meal" type="checkbox" class="cbx" id="333kid2_<?=$i?>">
                                                        <label class="label-cbx" for="333kid2_<?=$i?>">
                                                            <span class="cbx-cnt">  ДЕТСКИЕ БЛЮДА</span>
                                                        </label>

                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block   cbx-block--16  ">
                                                        <input name="tour_baby_<?=$i?>[]" value="changing_table" type="checkbox" class="cbx" id="333kid3_<?=$i?>">
                                                        <label class="label-cbx" for="333kid3_<?=$i?>">
                                                            <span class="cbx-cnt">ПЕЛЕНАЛЬНЫЙ СТОЛИК</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block   cbx-block--16  ">
                                                        <input name="tour_baby_<?=$i?>[]" value="animation" type="checkbox" class="cbx" id="333kid4_<?=$i?>">
                                                        <label class="label-cbx" for="333kid4_<?=$i?>">
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
                                                        <input name="tour_other_<?=$i?>[]" value="animation" type="checkbox" class="cbx" id="333other1_<?=$i?>">
                                                        <label class="label-cbx" for="333other1_<?=$i?>">
                                                            <span class="cbx-cnt">ВЕСЕЛАЯ АНИМАЦИЯ</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-dropdown-stars__item ">
                                                    <div class="cbx-block    cbx-block--16 ">
                                                        <input name="tour_other_<?=$i?>[]" value="parties" type="checkbox" class="cbx" id="333other2_<?=$i?>">
                                                        <label class="label-cbx" for="333other2_<?=$i?>">
                                                            <span class="cbx-cnt">  ТУСОВКИ РЯДОМ С ОТЕЛЕМ </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="formDirections__btn-orange submit-hotel-params">Применить</div>
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
                                    <select class="sumo-department"></select>
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
                                                <input name="meal[]" value="any" type="checkbox" class="cbx" id="8eat2-type0">
                                                <label class="label-cbx" for="8eat2-type0">
                                                    <span class="cbx-cnt">ЛЮБОЕ</span>
                                                </label>

                                            </div>
                                        </div>
                                        <div class="form-dropdown-stars__item ">
                                            <div class="cbx-block    cbx-block--16 ">
                                                <input name="meal[]" value="AI" type="checkbox" class="cbx" id="8eat2-type1">
                                                <label class="label-cbx" for="8eat2-type1">
                                                    <span class="cbx-cnt">AI Все включено</span>
                                                </label>

                                            </div>
                                        </div>
                                        <div class="form-dropdown-stars__item ">
                                            <div class="cbx-block    cbx-block--16">
                                                <input name="meal[]" value="FB" type="checkbox" class="cbx" id="8eat2-type2">
                                                <label class="label-cbx" for="8eat2-type2">
                                                    <span class="cbx-cnt">FB  Завтрак + обед + ужин</span>
                                                </label>

                                            </div>
                                        </div>
                                        <div class="form-dropdown-stars__item ">
                                            <div class="cbx-block    cbx-block--16 ">
                                                <input name="meal[]" value="HB" type="checkbox" class="cbx" id="8eat2-type3">
                                                <label class="label-cbx" for="8eat2-type3">
                                                    <span class="cbx-cnt">HB  Завтрак +  ужин</span>
                                                </label>

                                            </div>
                                        </div>
                                        <div class="form-dropdown-stars__item ">
                                            <div class="cbx-block    cbx-block--16 ">
                                                <input name="meal[]" value="BB" type="checkbox" class="cbx" id="8eat2-type4">
                                                <label class="label-cbx" for="8eat2-type4">
                                                    <span class="cbx-cnt"> BB Завтрак</span>
                                                </label>

                                            </div>
                                        </div>
                                        <div class="form-dropdown-stars__item ">
                                            <div class="cbx-block   cbx-block--16  ">
                                                <input name="meal[]" value="RO" type="checkbox" class="cbx" id="8eat2-type5">
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

                <div class="tour-selection-wrap">
                <? for ($i = 0; $i < 3; $i++) { ?>
                    <div data-tour-row="<?=$i?>" class="tour-selection-wrap-in tour-selection-wrap-flex <? if ($i > 0) { ?>tour-selection-wrap-in--hidden<? } ?>">
                        <div class="tour-selection-field tour-selection-field--740">
                            <div class="bth__inp-block js-show-formDirections js-formDirections--big-mobile">
                                <span class="bth__inp-lbl ">Добавить отель</span>
                                <span class="bth__inp">
                                    <span class="hotel-search">
                                        <span class="hotel-search__cut"></span>
                                        <span class="hotel-search__rating"></span>
                                        <span class="hotel-search__place"></span>
                                    </span>
                                </span>
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


                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                        <? if ($i == 0) { ?>
                            <span class="tour-selection-plus hide-1023 js-add-hotel"><i class="fas fa-plus"></i></span>
                        <? } else { ?>
                            <span class=" tour-selection-plus js-del-hotel"><i class="fas fa-minus"></i></span>
                        <? } ?>
                    </div>
                    <? } ?>
                </div>

            </div>


            <div class="tour-selection-wrap-in">
                <div class="bth__ta-resizable-wrap">
                    <div class="bth__ta-resizable" contenteditable=""></div>

                    <span class="bth__ta-resizable-hint">Дополнительные пожелания</span>

                </div>
            </div>
            <div class="tour-selection-wrap-in">
                <div class=" bth__btn  bth__btn--fill bth__loader" data-submit-step="1">
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