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



    $('.btn-custom-order').on('click', function() {
        var buttonCustomBooking = $(this);
        var fieldSelectors = {
            'parametrs': '#parametrs',
            'name': '#name1',
            'phone': '#phone1',
            'email': '#mail3'
        };

        $('#formPanel').find('.has-error').removeClass('has-error');
        buttonCustomBooking.addClass('bth__loader--animate');

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
            console.log('Error');
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
            console.log('Error');
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
    console.log(wrapperSelect);
    console.log(name);
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
 * загружает список городов
 */
initDirectionCitySelect = function(countryId, tourRowNumber) {
    $.ajax({
        url: '/dictionary/cities',
        data: {countryId: countryId},
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log(response);
            addCitiesSelect(response, tourRowNumber);
        },
        error: function() {
            console.log('Error');
        }
    });
};


addCitiesSelect = function(jsonCities, tourRowNumber) {
    var selectCity = $('['+tourRowAttrSelector+'="'+tourRowNumber+'"]').find(selectorDirectionCity);
    console.log('['+tourRowAttrSelector+'="'+tourRowNumber+'"]');
    jsonCities.forEach(function(item) {
        selectCity[0].sumo.add(item.id, item.name);
    });

};


setFieldError = function(selector, textHint = 'Поле не должно быть пустым') {
    var inputWrapper = $(selector).parents('.bth__inp-block');
    inputWrapper.addClass('has-error');
    inputWrapper.find('.bth__cnt').text(textHint);
};

setFormWrapperHeight = function() {
    var wrapper = $('.form-panel__wrapper');
    var height = wrapper.outerHeight();
    wrapper.parents('div').css({'min-height': height});
};


setCountryFlag = function(tourRowNumber, imageFlag) {
    console.log('flag', imageFlag);
    if (imageFlag === '') {
        return;
    }
    var inputWrapper = $('['+tourRowAttrSelector+'="'+tourRowNumber+'"]').find('.bth__inp-block--direction');
    inputWrapper.addClass('bth__inp-block--has-flag');
    inputWrapper.find('.bth__inp-lbl').addClass('bth__inp-lbl--center');
    inputWrapper.find('.tour-selection__flag').css({"background-image":"url('"+imageFlag+"')"});
};

resetCountryFlag = function(tourRowNumber) {
    var inputWrapper = $('['+tourRowAttrSelector+'="'+tourRowNumber+'"]').find('.bth__inp-block');
    inputWrapper.removeClass('bth__inp-block--has-flag');
    inputWrapper.find('.bth__inp-block--direction .bth__inp-lbl').removeClass('bth__inp-lbl--center');
    inputWrapper.find('.tour-selection__flag').css({"background-image":"none"});
};