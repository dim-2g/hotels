var selectorDirection = '.sumo-direction';
var selectorDirectionCity = '.sumo-direction-city';
var tourRowAttrSelector = 'data-tour-row';
var selectorDepartmentCity = '.sumo-department';
var selectorTouristCity = '#sumo-list-city';
var fieldSelectorsComplexForm = {
    'order_id': '.order-form__step-2 [name="order_id"]',
    'tourist_city': '.order-form__step-2 [name="tourist_city"]',
    'name': '.order-form__step-2 #name3',
    'phone': '.order-form__step-2 #phone3',
    'email': '.order-form__step-2 #mail2'
};
var orderTour = {
    directions: [],
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

var orderHotel = {
    departmentId: '',
    meal: [],
    hotels: [],
    addHotelParam: function(index, key, value) {
        if (this.hotels[index]) {
            this.hotels[index][key] = value;
        } else {
            this.hotels[index] = {[key]: value};
        }
        this.hotels[index].active = 1;
    },
    // Ставим признак неактивности, когда нажимаем на минус, чтобы не отправлять данные в заявке
    // но при этом и не удаляем эти данные
    hideHotel: function(index) {
        this.hotels[index].active = 0;
    }
};

$(document).ready(function () {

    //переопределяем функции поиска выпадающих списков, согласно ТЗ
    reinitSumoSearch('.sumo-direction');
    reinitSumoSearch('.sumo-direction-city');
    reinitSumoSearch('.sumo-department');
    reinitSumoSearch('#sumo-list-city', 'reinitSumoSearchFuncTouristCity');
    //визуально устанавливаем дефолтные значения
    setSumoSelect($(selectorDirection), 'укажите страну');
    setSumoSelect($(selectorDirectionCity), 'не важно');
    setSumoSelect($(selectorDepartmentCity), 'без перелета');
    setHotelMeal('.js-types-search-hotel-blocks [name="meal[]"]', 'any');
    //загружаем список стран
    initDirectionSelect();
    //загружаем города вылета
    initDepartmentCitySelect();
    //при клике на контрол с выпадающим списком
    $('body').on('click', '.js-show-formDirections', function() {
        //скроем все открытые списки, кроме текущего
        $('.js-show-formDirections').not(this).parent().find('.formDirections').hide();
        //получаем номер строки на которой производим действия
        var tourRowNumber = findCurrentRowNumber($(this));
        if (!orderTour.hasDirection(tourRowNumber)) {
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
        orderTour.addDirection(tourRowNumber, 'countryId', countryId);
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
        orderTour.addDirection(tourRowNumber, 'cityId', cityId);
    });

    //при клике на городе туриста
    $('body').on('change', selectorTouristCity, function() {
        var cityId = $(this).val();
        var cityName = $(this).find('option:selected').text().trim();
        //визуально показываем какой город выбран
        setSumoSelect($(this), cityName, cityId);
        $(fieldSelectorsComplexForm.tourist_city).val(cityId);
    });

    //при клике на городе вылета
    $('body').on('change', selectorDepartmentCity, function() {
        var cityId = $(this).val();
        var cityName = $(this).find('option:selected').text().trim();
        //визуально показываем какой город выбран
        setSumoSelect($(this), cityName, cityId);
        //получаем номер строки на которой производим действия
        var tourRowNumber = findCurrentRowNumber($(this));

        var orderType = findActiveTab();
        if (orderType == 'tours') {
            //добавляем в объект заказа выбранный город вылета
            orderTour.addDirection(tourRowNumber, 'departmentId', cityId);
        }
        if (orderType == 'hotel') {
            orderHotel.departmentId = cityId;
        }
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
        }
    });

    $('.js-del-field').on('click', function () {
        var currentTourRow = $(this).parents('['+tourRowAttrSelector+']');
        currentTourRow.addClass('tour-selection-wrap-in--hidden');
        var tourRowNumber = findCurrentRowNumber($(this));
        orderTour.hideDirection(tourRowNumber);
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
        orderHotel.hideDirection(tourRowNumber);
    });

    $('body').on('keyup', '.formDirections__search input.bth__inp', function() {
        var searchText = $(this).val();
        if (searchText.length < 3) {
            return;
        }
        findHotels(searchText);
    });

    $('body').on('click', '[data-hotel-name]', function() {
        var hotelRowNumber = findCurrentRowNumber($(this));
        //скрываем скринап
        $(this).parents('.formDirections').hide();

        var hotelResultWrapper = $('['+tourRowAttrSelector+'="'+hotelRowNumber+'"]');
        hotelResultWrapper.find('.bth__inp-lbl').addClass('active');
        hotelResultWrapper.find('.hotel-search__cut').text( $(this).attr('data-hotel-name') );
        hotelResultWrapper.find('.hotel-search__place').text( ', ' + $(this).attr('data-hotel-country') + ', ' + $(this).attr('data-hotel-city'));
        hotelResultWrapper.find('.hotel-search__rating').text( $(this).attr('data-hotel-rating') );
        //добавляем данные к заказу
        orderHotel.addHotelParam(hotelRowNumber, 'hotelId', $(this).attr('data-hotel-id'));
    });

    //обрабатываем выбор Питания на вкладке Конкретный отель
    $('body').on('click', '[name="meal[]"]', function() {
        var currentValue = $(this).val();
        var inputAny = $('.js-types-search-hotel-blocks [name="meal[]"][value="any"]');
        if (currentValue == 'any') {
            $('.js-types-search-hotel-blocks [name="meal[]"]').not('[value="any"]').prop("checked", false);
        } else {
            inputAny.prop("checked", false);
            //проверим, если ниодно не выбрано, установим Любое
        }
        if ($('.js-types-search-hotel-blocks [name="meal[]"]:checked').length == 0) {
            inputAny.prop("checked", true);
        }
        //записываем выбранные значения
        orderHotel.meal = [];
        $('.js-types-search-hotel-blocks [name="meal[]"]:checked').each(function(i) {
            orderHotel.meal.push($(this).val());
        });
        //отображаем выбранные значения
        mealString = orderHotel.meal.join(' ');
        if (mealString == 'any') {
            mealString = 'ЛЮБОЕ'
        }
        setSumoSelect($(this), mealString);
    });

    //логика выбора категории отеля
    $('body').on('click', '[name^="tour_category"]', function() {
        var currentValue = $(this).val();
        var attrName = $(this).attr('name');
        if (currentValue == 'any') {
            $('.js-types-search-tours-blocks [name="'+attrName+'"]').not('[value="any"]').prop("checked", false);
        } else {
            $('.js-types-search-tours-blocks [name="'+attrName+'"][value="any"]').prop("checked", false);
        }
    });

    //обрабатываем выбор Питания на вкладке Турпакет
    $('body').on('click', '[name^="tour_meal"]', function() {
        var currentValue = $(this).val();
        var attrName = $(this).attr('name');
        if (currentValue == 'any') {
            $('.js-types-search-tours-blocks [name="'+attrName+'"]').not('[value="any"]').prop("checked", false);
        } else {
            $('.js-types-search-tours-blocks [name="'+attrName+'"][value="any"]').prop("checked", false);
        }
    });

    //обрабатываем выбор Расположения на вкладке Турпакет
    $('body').on('click', '[name^="tour_place"]', function() {
        var currentValue = $(this).val();
        var attrName = $(this).attr('name');
        if (currentValue == 'any') {
            $('.js-types-search-tours-blocks [name="'+attrName+'"]').not('[value="any"]').prop("checked", false);
        } else {
            $('.js-types-search-tours-blocks [name="'+attrName+'"][value="any"]').prop("checked", false);
            var rowNumber = findCurrentRowNumber($(this));
            if (isOtherCategoryPlace(currentValue, rowNumber)) {
                $('.js-types-search-tours-blocks [name="'+attrName+'"]').not('[value="'+currentValue+'"]').prop("checked", false);
            }
        }
    });

    //обрабатываем сохранение Параметров отеля
    $('body').on('click', '.submit-hotel-params', function() {
        var rowNumber = findCurrentRowNumber($(this));
        var hotelParams = findCheckedTourParams(rowNumber);
        setLabelHotelParamsControl(rowNumber);
        $(this).parents('.formDirections').hide();
        orderTour.addDirection(rowNumber, 'params', hotelParams);
    });

    //сабмит первого шанга
    $('body').on('click', '[data-submit-step="1"]', function() {
        var step = parseInt($(this).attr('data-submit-step'));
        var orderData = {
            params: {},
            general: {},
            tour: {},
            hotels: {}
        };
        orderData.params.step = step;
        orderData.params.wish = $('.order-wish').text();
        orderData.params.order_type = findActiveTab();
        orderData.general = lsfw.bookingRequest;
        orderData.tour.items = orderTour.directions;

        orderData.hotels.departmentId = orderHotel.departmentId;
        orderData.hotels.meal = orderHotel.meal;
        orderData.hotels.items = orderHotel.hotels;
        storeOrder(orderData);
    });

    //сабмит второго шанга
    $('body').on('click', '[data-submit-step="2"]', function(e) {
        e.preventDefault();
        var data = {};
        data.order_id = $(fieldSelectorsComplexForm.order_id).val();
        data.tourist_city = $(fieldSelectorsComplexForm.tourist_city).val();
        data.name = $(fieldSelectorsComplexForm.name).val();
        data.phone = $(fieldSelectorsComplexForm.phone).val();
        data.email = $(fieldSelectorsComplexForm.email).val();
        storeOrderStep2(data);
    });

    //Принудительно корректируем ширину активного таба
    setTimeout(function() {
        correctWidthUnderline();
    }, 500);

});

//устанавливаем значение по умолчанию для простого селектора
//selector - селектор input, который установить как выбранный по-умолчанию
//defaultCheckedValue - значение селектора, которое должно быть выбрано
var setHotelMeal = function(selector, defaultCheckedValue) {
    var targetInput = $(selector+'[value="'+defaultCheckedValue+'"]');
    targetInput.prop("checked", true);
    setSumoSelect($(selector), targetInput.next().text());
};

// Принудительно корректируем ширину активного таба, если хеш
// отличается от id текущего таба.
var correctWidthUnderline = function() {
    var activeTab = $('.tour-selection-box .tabs-bar .tab.active');
    var underline = $('.tour-selection-box .tabs-bar .line');
    if (activeTab.length > 0) {
        activeTabWidth = activeTab.width();
        underline.css({"width": activeTabWidth});
    }
};

//Добавляем заказ данными со второго шага
var storeOrderStep2 = function(data) {
    var buttonCustomBooking = $('[data-submit-step="2"]');
    $('#step1Panel').find('.has-error').removeClass('has-error');

    var timeoutAnimation = setTimeout(function(){
        buttonCustomBooking.addClass('bth__loader--animate');
    }, 500);

    $.ajax({
        url: '/booking/store-add',
        data: data,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                setFormWrapperHeight('#step1Panel .form-panel__wrapper');
                $('#step1Panel .form-panel__wrapper').hide();
                $('#step1Panel .form-panel__success').fadeIn(500);
            } else {
                clearTimeout(timeoutAnimation);
                response.errors.forEach(function(item) {
                    setFieldError(fieldSelectorsComplexForm[item.key], item.text);
                });
            }
            buttonCustomBooking.removeClass('bth__loader--animate');
        },
        error: function() {
            console.log('Error in method storeOrderStep2');
            buttonCustomBooking.removeClass('bth__loader--animate');
        }
    });
};

// Записываем данные из Сложной формына первом шаге
var storeOrder = function(orderData) {
    $.ajax({
        url: '/booking/store',
        data: orderData,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if (response.data.id > 0) {
                    $('[name="order_id"]').val(response.data.id);
                    $('.order-form__step-1').hide();
                    $('.order-form__step-2').fadeIn();
                } else {
                    console.log('Error in method storeOrder');
                }
            }
        },
        error: function() {
            console.log('Error');

        }
    });
};

// проверяем текущее место из тех же категорий, что и выбраны ранее
// currentPlace - текущее значение места
// rowNumber - номер строки (0, 1 или 2)
var isOtherCategoryPlace = function(currentPlace, rowNumber) {
    var currentCategory = findCategoryTourPlaces(currentPlace);
    var checkedCategories = findCheckedTourPlaces(rowNumber);
    var resultCompare = false;
    checkedCategories.forEach(function(item) {
        categoryIteration = findCategoryTourPlaces(item);
        if (currentCategory != categoryIteration) {
            resultCompare = true;
        }
    });
    return resultCompare;
};

//устанавливаем заголовок у контрола Параметры отеля
var setLabelHotelParamsControl = function(rowNumber) {
    var rowWrapper = $('.js-types-search-tours-blocks [data-tour-row="'+rowNumber+'"]');
    //найдем кол-во параметров
    var countParams = rowWrapper.find('[name^="tour_"]').length;
    //найдем кол-во выбранных параметров
    var countActiveParams = rowWrapper.find('[name^="tour_"]:checked').length;
    //добавляю в разметку
    rowWrapper.find('.bth__inp-lbl').addClass('active');
    rowWrapper.find('.bth__inp-block--hotel-params .bth__inp').text(countActiveParams + ' / ' + countParams + ' параметров');
};

//собираем все данные по выделенным параметрам Параметра отеля
//tourRowNumber - номер строки, по которой собираем данные
var findCheckedTourParams = function(tourRowNumber) {
    var params = {
        tour_category: [],
        tour_rating: [],
        tour_meal: [],
        tour_place: [],
        tour_baby: [],
        tour_other: []
    };

    $('.js-types-search-tours-blocks [data-tour-row="'+tourRowNumber+'"] [name^="tour_category"]:checked').each(function(i) {
        params.tour_category.push($(this).val());
    });
    $('.js-types-search-tours-blocks [data-tour-row="'+tourRowNumber+'"] [name^="tour_rating"]:checked').each(function(i) {
        params.tour_rating.push($(this).val());
    });
    $('.js-types-search-tours-blocks [data-tour-row="'+tourRowNumber+'"] [name^="tour_meal"]:checked').each(function(i) {
        params.tour_meal.push($(this).val());
    });
    $('.js-types-search-tours-blocks [data-tour-row="'+tourRowNumber+'"] [name^="tour_place"]:checked').each(function(i) {
        params.tour_place.push($(this).val());
    });
    $('.js-types-search-tours-blocks [data-tour-row="'+tourRowNumber+'"] [name^="tour_baby"]:checked').each(function(i) {
        params.tour_baby.push($(this).val());
    });
    $('.js-types-search-tours-blocks [data-tour-row="'+tourRowNumber+'"] [name^="tour_other"]:checked').each(function(i) {
        params.tour_other.push($(this).val());
    });

    return params;
};

// находим все выделенные маста в Расположении
// rowNumber - номер строки для поиска
var findCheckedTourPlaces = function(rowNumber) {
    var arr = [];
    $('.js-types-search-tours-blocks [name="tour_place_'+rowNumber+'[]"]:checked').each(function(i) {
        arr.push($(this).val());
    });
    return arr;
};

// возвращает категорию места
var findCategoryTourPlaces = function(place) {
    var category = place.split('_');
    return category[0];
};

// Определяем активную вкладку
var findActiveTab = function() {
    return $('[name="types"]:checked').val();
};

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
setFormWrapperHeight = function(selector = '.form-panel__wrapper') {
    var wrapper = $(selector);
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
        sumoSelect.each(function(index, select) {
            select.sumo.Search = window[func](select.sumo);
        });

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

        searchText = encodeURIComponent(O.ftxt.val());
        P.html('Нет совпадений для "{0}"'.replace(/\{0\}/g, searchText)).toggle(!hid.length);

        O.selAllState();
    });
};

// Переопределяем фуникцию поиска, для Выбора города туриста
// поиска, после ввода 3х символов
reinitSumoSearchFuncTouristCity = function(sumoSelect){
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
        findTouristCities($(this).val());

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

var findTouristCities = function(query) {
    var elementSelect = $(selectorTouristCity).get(0);
    elementSelect.sumo.removeAll();
    $.ajax({
        url: '/dictionary/city-tourist',
        data: {query: query},
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            response.forEach(function(item) {
                elementSelect.sumo.add(item.id, item.name);
            });
        },
        error: function() {
            console.log('Error');
        }
    });
};

getSearchItemHotelTemplate = function(hotelId, countryName, hotelName, starRating, cityName, flagImage) {
    var tmpl = `
        <div class="formDirections__bottom-item"
            data-hotel-id="${hotelId}" 
            data-hotel-country="${countryName}"
            data-hotel-name="${hotelName}"
            data-hotel-rating="${starRating}"
            data-hotel-city="${cityName}"
            >
            <div class="formDirections__city">
                <div class=" lsfw-flag lsfw-flag--30w lsfw-flag-1" style="background-image: url('${flagImage}')">
                    <div class="hint">${countryName}</div>
                </div>
                <span class="formDirections__cut"> ${hotelName} </span> ${starRating}
            </div>
            <span class="formDirections__count">${cityName}</span>
        </div>
    `;
    return tmpl;
};

// Заполняем контрол данными по отелям
// query - подстрока для поиска вхождения в названии отелей
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
                var itemSearch = getSearchItemHotelTemplate(
                    item.id,
                    item.country_name,
                    item.name,
                    item.hotel_category,
                    item.resort_name,
                    item.flag_image);
                hotelsWrapper.append(itemSearch);
            });
        },
        error: function() {
            console.log('Error');
        }
    });
};