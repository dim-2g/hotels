var selectorDirection = '.sumo-direction';
var selectorDirectionCity = '.sumo-direction-city';
var tourRowAttrSelector = 'data-tour-row';
var selectorDepartmentCity = '.sumo-department';

$(document).ready(function () {

    setSumoSelect($(selectorDirection), 'не важно');
    setSumoSelect($(selectorDirectionCity), 'не важно');
    setSumoSelect($(selectorDepartmentCity), 'без перелета');

    initDirectionSelect();
    initDepartmentCitySelect();

    //при клике на контрол с выпадающим списком
    $('body').on('click', '.js-show-formDirections', function() {
        $(this).parent().find('.formDirections').slideDown();
    });

    //при клике на конкретной стране
    $('body').on('change', selectorDirection, function() {
        var countryId = $(this).val();
        var countryName = $(this).find('option:selected').text().trim();
        var countryFlag = $(this).find('option:selected').attr('data-flag');
        var tourRowNumber = $(this).parents('['+tourRowAttrSelector+']').attr(tourRowAttrSelector);
        resetCountryFlag(tourRowNumber);
        setSumoSelect($(this), countryName, countryId);
        setCountryFlag(tourRowNumber, countryFlag);
        initDirectionCitySelect(countryId, tourRowNumber);

        console.log('req', lsfw.bookingRequest);
    });

    //при клике на конкретном городе
    $('body').on('change', selectorDirectionCity, function() {
        var cityId = $(this).val();
        var cityName = $(this).find('option:selected').text().trim();
        setSumoSelect($(this), cityName, cityId);
    });

    //при клике на городе вылета
    $('body').on('change', selectorDepartmentCity, function() {
        var cityId = $(this).val();
        var cityName = $(this).find('option:selected').text().trim();
        setSumoSelect($(this), cityName, cityId);
    });


    //при клике на Отправить в нестандартном запросе
    $('.btn-custom-order').on('click', function() {
        var buttonCustomBooking = $(this);
        submitCustomForm(buttonCustomBooking);

    });

    $('.js-add-field').on('click', function () {
        var hiddenTourRow = $('.tour-selection-wrap-in--hidden:eq(0)');
        if (hiddenTourRow.length > 0) {
            hiddenTourRow.removeClass('tour-selection-wrap-in--hidden');
        };
    });

    $('.js-del-field').on('click', function () {
        var currentTourRow = $(this).parents('['+tourRowAttrSelector+']');
        currentTourRow.addClass('tour-selection-wrap-in--hidden');
    });


});

/*
 * отправляем форму НЕстандартного запроса
 * buttonCustomBooking - объект кнопки сабмита
 */
submitCustomForm = function(buttonCustomBooking) {
    var fieldSelectors = {
        'parametrs': '#parametrs',
        'name': '#name1',
        'phone': '#phone1',
        'email': '#mail3'
    };

    $('#formPanel').find('.has-error').removeClass('has-error');
    /*
    buttonCustomBooking.addClass('bth__loader--animate');
    проверка осуществляется только на бекэнде, так как в ТЗ ничего по данному
    поводу не было указано и анимацю я запускал тут сразу,
    но после проверки тестировщиком потребовалось анимацию запускать,
    только если далее следует успешная отправка. Поэтому сейчас добавил отложенный запуск анимации,
    а при возвращении ошибки очищаю таймаут. Если сервер подвиснет, при обработке полей,
    то через полсекунды запустится анимация, и пользователь будет понимать, что процесс выполняется
    */

    var timeoutAnimation = setTimeout(function(){
        buttonCustomBooking.addClass('bth__loader--animate');
    }, 500);

    $.ajax({
        url: '/booking/custom',
        data: {
            'parametrs': $(fieldSelectors.parametrs).val(),
            'name': $(fieldSelectors.name).val(),
            'phone': $(fieldSelectors.phone).val(),
            'email': $(fieldSelectors.email).val()
        },
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                setFormWrapperHeight();
                $('.form-panel__wrapper').hide();
                $('.form-panel__success').fadeIn(500);
            } else {
                clearTimeout(timeoutAnimation);
                response.errors.forEach(function(item) {
                    setFieldError(fieldSelectors[item.key], item.text);
                });
            }
            buttonCustomBooking.removeClass('bth__loader--animate');
        },
        error: function() {
            console.log('Error');
            buttonCustomBooking.removeClass('bth__loader--animate');
        }
    });
};

/*
 * загружает список стран
 */
initDirectionSelect = function() {
    $.ajax({
        url: '/dictionary/countries',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            addCountryInAllSelects(response);
        },
        error: function() {
            console.log('Error in method initDirectionSelect');
        }
    });
};

/*
 * загружает список Городов для вылета
 */
initDepartmentCitySelect = function() {
    $.ajax({
        url: '/dictionary/department',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            addDepartmentCityInAllSelects(response);
        },
        error: function() {
            console.log('Error in method initDepartmentCitySelect');
        }
    });
};


/*
 * Устанавливает Название поля, при выборе выпадающего селекта
 * selector - селектор тега select
 * name - видимое название
 * value - значение
 */
setSumoSelect = function(element, name, value = 0) {
    var wrapperSelect = element.parents('.tour-selection-field');
    var labelSelect = wrapperSelect.find('.bth__inp-lbl');
    var textSelect = wrapperSelect.find('.bth__inp');
    if (name !== '') {
        labelSelect.addClass('active');
        textSelect.text(name);
    }
};

/*
 * Добавялет элемент во все выпадающие списки
 * selector - селектор тега select
 * name - видимое название
 * value - значение
 */
addSumoSelect = function(elementHtml, name, value = 0) {
    elementHtml.sumo.add(value, name);
};

/*
 * Добавляет во все выпадающие списки информацию по странам
 * jsonCountry - набор данных по странам
 */
addCountryInAllSelects = function(jsonCountry) {
    $(selectorDirection).each(function(index, elementSelect) {
        jsonCountry.forEach(function(item) {
            var option = new Option(item.name, item.id);
            $(option).attr("data-flag", item.flag_image);
            elementSelect.sumo.addHTML(option);
        });
    });
};

/*
 * Добавляет во все выпадающие списки информацию по городам вылета
 * jsonCountry - набор данных по странам
 */
addDepartmentCityInAllSelects = function(jsonCities) {
    $(selectorDepartmentCity).each(function(index, elementSelect) {
        jsonCities.forEach(function(item) {
            elementSelect.sumo.add(item.id, item.name);
        });
    });
};


/*
 * загружает список городов для конкретной страны
 * countryId - id страны из словаря
 */
initDirectionCitySelect = function(countryId, tourRowNumber) {
    $.ajax({
        url: '/dictionary/cities',
        data: {countryId: countryId},
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            addCitiesSelect(response, tourRowNumber);
        },
        error: function() {
            console.log('Error');
        }
    });
};

/*
 * Загружаем список городов в нужный селект
 */
addCitiesSelect = function(jsonCities, tourRowNumber) {
    var selectCity = $('['+tourRowAttrSelector+'="'+tourRowNumber+'"]').find(selectorDirectionCity);
    selectCity[0].sumo.removeAll();
    jsonCities.forEach(function(item) {
        selectCity[0].sumo.add(item.id, item.name);
    });

};

/*
 * Выводим сообщение об ошибки заполнения поля
 * устанавливаем текст подсказки
 */
setFieldError = function(selector, textHint = 'Поле не должно быть пустым') {
    var inputWrapper = $(selector).parents('.bth__inp-block');
    inputWrapper.addClass('has-error');
    inputWrapper.find('.bth__cnt').text(textHint);
};

/*
 * Фиксируем высоту контейнера формы, чтобы после успешной отправки
 * не прыгала высота
 */
setFormWrapperHeight = function() {
    var wrapper = $('.form-panel__wrapper');
    var height = wrapper.outerHeight();
    wrapper.parents('div').css({'min-height': height});
};

/*
 * устанавливаем изображение флага в селект
 * отодвигаем название лейбла
 * tourRowNumber - номер строки, в которой выбрали страну
 * imageFlag - путь до картинки
 */
setCountryFlag = function(tourRowNumber, imageFlag) {
    if (imageFlag === '') {
        return;
    }
    var inputWrapper = $('['+tourRowAttrSelector+'="'+tourRowNumber+'"]').find('.bth__inp-block--direction');
    inputWrapper.addClass('bth__inp-block--has-flag');
    inputWrapper.find('.bth__inp-lbl').addClass('bth__inp-lbl--center');
    inputWrapper.find('.tour-selection__flag').css({"background-image":"url('"+imageFlag+"')"});
};

/*
 * сбрасываем изображение флага в селекте
 * tourRowNumber - номер строки, в которой выбрали страну
 */
resetCountryFlag = function(tourRowNumber) {
    var inputWrapper = $('['+tourRowAttrSelector+'="'+tourRowNumber+'"]').find('.bth__inp-block');
    inputWrapper.removeClass('bth__inp-block--has-flag');
    inputWrapper.find('.bth__inp-block--direction .bth__inp-lbl').removeClass('bth__inp-lbl--center');
    inputWrapper.find('.tour-selection__flag').css({"background-image":"none"});
};