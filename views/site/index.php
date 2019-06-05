<?php

/* @var $this yii\web\View */

$this->title = 'TopHotels';
?>
<div class="tabs-block">
    <div class="tabs-bar   tabs-bar--responsive js-768-tabs">
        <div id="form" class="tab active">Нестандартный запрос</div>
        <div class="line" style="width: 130px"></div>
    </div>


    <div class="panel" id="formPanel" >

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

                    </div>
                </div>

            </div>


            <div class="tour-selection-wrap-in">
                <div class=" bth__btn  bth__btn--fill bth__loader">
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

            <button class="test-btn">Проверка</button>
        </div>


    </div>


</div>


<?php
$js = <<<JS
    $('.test-btn').on('click', function() {
       $.ajax({
            url: '/booking/custom',
            data: {
                "message": "123", 
                "name-disabled": "Petr", 
                "phone": "8(765)432-10-10", 
                "email":"test@test.ru"
            },
            type: "POST",
            success: function() {
                console.log('OK');
            },
            error: function() {
                console.log('Error');
            }
       }); 
    });
JS;

$this->registerJs($js);
?>