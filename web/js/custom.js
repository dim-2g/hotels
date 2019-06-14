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
        var tourRowNumber = $(this).parents('['+tourRowAttrSelector+']').attr(tourRowAttrSelector);
        setSumoSelect($(this), countryName, countryId);
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
            elementSelect.sumo.add(item.id, item.name);
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