var selectorDirection = '.sumo-direction';
var selectorDirectionCity = '.sumo-direction-city';
var tourRowAttrSelector = 'data-tour-row';
var selectorDepartmentCity = '.sumo-department';
var tour = {
    directions: [],
    hotels: [],

    addDirection: function(index, key, value) {
        if (this.directions[index]) {
            this.directions[index][key] = value;
        } else {
            this.directions[index] = {[key]: value};
        }
        this.directions[index].active = 1;
    },

    hasDirection: function(index) {
        if (this.directions[index]) {
            if (this.directions[index] !== '') {
                return true;
            }
        }
        return false;
    },

    // Ставим признак неактивности, когда нажимаем на минус, чтобы не отправлять данные в заявке
    // но при этом и не удаляем эти данные
    hideDirection: function(index) {
        this.directions[index].active = 0;
    }

};

$(document).ready(function () {

    //переопределяем функции поиска выпадающих списков, согласно ТЗ
    reinitSumoSearch('.sumo-direction');
    reinitSumoSearch('.sumo-direction-city');
    reinitSumoSearch('.sumo-department');
    //визуально устанавливаем дефолтные значения
    setSumoSelect($(selectorDirection), 'укажите страну');
    setSumoSelect($(selectorDirectionCity), 'не важно');
    setSumoSelect($(selectorDepartmentCity), 'без перелета');
    //загружаем список стран
    initDirectionSelect();
    //загружаем города вылета
    initDepartmentCitySelect();
    //при клике на контрол с выпадающим списком
    $('body').on('click', '.js-show-formDirections', function() {
        //получаем номер строки на которой производим действия
        var tourRowNumber = findCurrentRowNumber($(this));
        if (!tour.hasDirection(tourRowNumber)) {
            //устанавливаем дефолтный заголовок выпадающго списка городов, если страна не выбрана
            setCaptionCitySelect(tourRowNumber, 'укажите страну');
        }
        //разворачиваем выпадающий список
        $(this).parent().find('.formDirections').slideDown();
    });

    //при клике на конкретной стране
    $('body').on('change', selectorDirection, function() {
        var countryId = $(this).val();
        var countryName = $(this).find('option:selected').text().trim();
        var countryFlag = $(this).find('option:selected').attr('data-flag');
        var tourRowNumber = $(this).parents('['+tourRowAttrSelector+']').attr(tourRowAttrSelector);
        //сбрасываем изображение флага
        resetCountryFlag(tourRowNumber);
        //визуально показываем какая страна выбрана
        setSumoSelect($(this), countryName, countryId);
        //устанавливаем изображение флага
        setCountryFlag(tourRowNumber, countryFlag);
        //заполняем селект с городами по выбранной стране
        initDirectionCitySelect(countryId, tourRowNumber);
        //устанавливаем заголовок выпадающго списка городов = названию страны
        setCaptionCitySelect(tourRowNumber, countryName);
        //добавляем в объект заказа выбранную страну
        tour.addDirection(tourRowNumber, 'countryId', countryId);
        console.log('tour', tour.directions);
    });

    //при клике на конкретном городе
    $('body').on('change', selectorDirectionCity, function() {
        var cityId = $(this).val();
        var cityName = $(this).find('option:selected').text().trim();
        //визуально показываем какой город выбран
        setSumoSelect($(this), cityName, cityId);
        //получаем номер строки на которой производим действия
        var tourRowNumber = findCurrentRowNumber($(this));
        //добавляем в объект заказа выбранный город
        tour.addDirection(tourRowNumber, 'cityId', cityId);
        console.log('tour', tour.directions);
    });

    //при клике на городе вылета
    $('body').on('change', selectorDepartmentCity, function() {
        var cityId = $(this).val();
        var cityName = $(this).find('option:selected').text().trim();
        //визуально показываем какой город выбран
        setSumoSelect($(this), cityName, cityId);
        //получаем номер строки на которой производим действия
        var tourRowNumber = findCurrentRowNumber($(this));
        //добавляем в объект заказа выбранный город вылета
        tour.addDirection(tourRowNumber, 'departmentId', cityId);
        console.log('tour', tour.directions);
    });


    //при клике на Отправить в нестандартном запросе
    $('.btn-custom-order').on('click', function() {
        var buttonCustomBooking = $(this);
        submitCustomForm(buttonCustomBooking);

    });

    $('.js-add-field').on('click', function () {
        var hiddenTourRow = $(this).parents('.tour-selection-wrap').find('.tour-selection-wrap-in--hidden:eq(0)');
        if (hiddenTourRow.length > 0) {
            hiddenTourRow.removeClass('tour-selection-wrap-in--hidden');
        };
    });

    $('.js-del-field').on('click', function () {
        var currentTourRow = $(this).parents('['+tourRowAttrSelector+']');
        currentTourRow.addClass('tour-selection-wrap-in--hidden');
        var tourRowNumber = findCurrentRowNumber($(this));
        tour.hideDirection(tourRowNumber);
    });

    $('.js-add-hotel').on('click', function () {
        var hiddenTourRow = $(this).parents('.tour-selection-wrap').find('.tour-selection-wrap-in--hidden:eq(0)');
        if (hiddenTourRow.length > 0) {
            hiddenTourRow.removeClass('tour-selection-wrap-in--hidden');
        };
    });

    $('.js-del-hotel').on('click', function () {
        var currentTourRow = $(this).parents('['+tourRowAttrSelector+']');
        currentTourRow.addClass('tour-selection-wrap-in--hidden');
    });

    $('body').on('keyup', '.formDirections__search input.bth__inp', function() {
        var searchText = $(this).val();
        if (searchText.length < 3) {
            return;
        }
        findHotels(searchText);
    });

});

// отправляем форму НЕстандартного запроса
// buttonCustomBooking - объект кнопки сабмита
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

// загружает список стран
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

// загружает список Городов для вылета
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

// Устанавливает Название поля, при выборе выпадающего селекта
// selector - селектор тега select
// name - видимое название
// value - значение
setSumoSelect = function(element, name, value = 0) {
    var wrapperSelect = element.parents('.tour-selection-field');
    var labelSelect = wrapperSelect.find('.bth__inp-lbl');
    var textSelect = wrapperSelect.find('.bth__inp');
    if (name !== '') {
        labelSelect.addClass('active');
        textSelect.text(name);
    }
};

// Добавялет элемент во все выпадающие списки
// selector - селектор тега select
// name - видимое название
// value - значение
addSumoSelect = function(elementHtml, name, value = 0) {
    elementHtml.sumo.add(value, name);
};

// Добавляет во все выпадающие списки информацию по странам
// jsonCountry - набор данных по странам
addCountryInAllSelects = function(jsonCountry) {
    $(selectorDirection).each(function(index, elementSelect) {
        jsonCountry.forEach(function(item) {
            var option = new Option(item.name, item.id);
            $(option).attr("data-flag", item.flag_image);
            elementSelect.sumo.addHTML(option);
        });
    });
};

// Добавляет во все выпадающие списки информацию по городам вылета
// jsonCountry - набор данных по странам
addDepartmentCityInAllSelects = function(jsonCities) {
    $(selectorDepartmentCity).each(function(index, elementSelect) {
        jsonCities.forEach(function(item) {
            elementSelect.sumo.add(item.id, item.name);
        });
    });
};

// загружает список городов для конкретной страны
// countryId - id страны из словаря
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

// Загружаем список городов в нужный селект
addCitiesSelect = function(jsonCities, tourRowNumber) {
    var selectCity = $('['+tourRowAttrSelector+'="'+tourRowNumber+'"]').find(selectorDirectionCity);
    selectCity[0].sumo.removeAll();
    jsonCities.forEach(function(item) {
        selectCity[0].sumo.add(item.id, item.name);
    });

};


// Выводим сообщение об ошибки заполнения поля
// устанавливаем текст подсказки
setFieldError = function(selector, textHint = 'Поле не должно быть пустым') {
    var inputWrapper = $(selector).parents('.bth__inp-block');
    inputWrapper.addClass('has-error');
    inputWrapper.find('.bth__cnt').text(textHint);
};

// Фиксируем высоту контейнера формы, чтобы после успешной отправки
// не прыгала высота
setFormWrapperHeight = function() {
    var wrapper = $('.form-panel__wrapper');
    var height = wrapper.outerHeight();
    wrapper.parents('div').css({'min-height': height});
};

// устанавливаем изображение флага в селект
// отодвигаем название лейбла
// tourRowNumber - номер строки, в которой выбрали страну
// imageFlag - путь до картинки
setCountryFlag = function(tourRowNumber, imageFlag) {
    if (imageFlag == undefined) {
        return;
    }
    var inputWrapper = $('['+tourRowAttrSelector+'="'+tourRowNumber+'"]').find('.bth__inp-block--direction');
    inputWrapper.addClass('bth__inp-block--has-flag');
    inputWrapper.find('.bth__inp-lbl').addClass('bth__inp-lbl--center');
    inputWrapper.find('.bth__inp').addClass('tour-selection__country');
    inputWrapper.find('.tour-selection__flag').css({"background-image":"url('"+imageFlag+"')"});
};

// сбрасываем изображение флага в селекте
// tourRowNumber - номер строки, в которой выбрали страну
resetCountryFlag = function(tourRowNumber) {
    var inputWrapper = $('['+tourRowAttrSelector+'="'+tourRowNumber+'"]').find('.bth__inp-block--direction');
    inputWrapper.removeClass('bth__inp-block--has-flag');
    inputWrapper.find('.bth__inp-lbl').removeClass('bth__inp-lbl--center');
    inputWrapper.find('.bth__inp').removeClass('tour-selection__country');
    inputWrapper.find('.tour-selection__flag').css({"background-image":"none"});
};

setCaptionCitySelect = function(tourRowNumber, caption) {
    var inputWrapper = $('['+tourRowAttrSelector+'="'+tourRowNumber+'"]').find('.bth__inp-block--direction-city').next();
    inputWrapper.find('.formDirections__top-tab').text(caption);
};


// Возвращает номер строки, на которой производятся действия, начиная с нуля.
// element - текущий элемент jQuery взаимодействия пользователя с формой
findCurrentRowNumber = function(element) {
    var tourRowNumber = element.parents('['+tourRowAttrSelector+']').attr(tourRowAttrSelector);
    return tourRowNumber;
};

// Обертка для переопределения функции поиска
reinitSumoSearch = function(selectorSumo, func = 'reinitSumoSearchFunc') {
    var sumoSelect = $(selectorSumo);
    if (sumoSelect.length > 0) {
        sumoSelect.get(0).sumo.Search = window[func](sumoSelect.get(0).sumo);
    }
};

// Переопределяем фуникцию поиска, чтобы реальзовать начало
// поиска, после ввода 3х символов
reinitSumoSearchFunc = function(sumoSelect){
    var O = sumoSelect,
        cc = O.CaptionCont.addClass('search'),
        P = $('<p class="no-match">');

    O.ftxt = $('<input type="text" class="search-txt" value="" placeholder="Искать...">')
        .on('click', function(e){
            e.stopPropagation();
        });
    cc.append(O.ftxt);
    O.optDiv.children('ul').after(P);

    O.ftxt.on('keyup.sumo',function(){
        if ($(this).val().length < 3) {
            return;
        }
        var hid = O.optDiv.find('ul.options li.opt').each(function(ix,e){
            e = $(e);
            if(e.text().toLowerCase().indexOf(O.ftxt.val().toLowerCase()) > -1)
                e.removeClass('hidden');
            else
                e.addClass('hidden');
        }).not('.hidden');

        P.html('Нет совпадений для "{0}"'.replace(/\{0\}/g, O.ftxt.val())).toggle(!hid.length);

        O.selAllState();
    });
};


getSearchItemHotelTemplate = function(countryName, hotelName, starRating, cityName) {
    var tmpl = `
        <div class="formDirections__bottom-item" 
            data-hotel-country="${countryName}"
            data-hotel-name="${hotelName}"
            data-hotel-rating="${starRating}"
            data-hotel-city="${cityName}"
            >
            <div class="formDirections__city">
                <div class=" lsfw-flag lsfw-flag--30w lsfw-flag-1">
                    <div class="hint">${countryName}</div>
                </div>
                <span class="formDirections__cut"> ${hotelName} </span>${starRating}
            </div>
            <span class="formDirections__count">${cityName}</span>
        </div>
    `;
    return tmpl;
};

findHotels = function(query) {
    var hotelsWrapper = $('.formDirections__bottom-blocks-cut');
    hotelsWrapper.html('');

    $.ajax({
        url: '/dictionary/hotels',
        data: {query: query},
        type: 'GET',
        dataType: 'json',
        success: function(response) {

            response.forEach(function(item) {
                elementSelect.sumo.add(item.id, item.name);
            });

            var itemSearch = getSearchItemHotelTemplate('Russia', item.name, '5*', 'Тамбов');
            hotelsWrapper.append(itemSearch);

        },
        error: function() {
            console.log('Error');
        }
    });
};