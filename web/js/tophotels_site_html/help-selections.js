$(document).ready(function () {
//Табы
    $('#step1').click(function () {
        line($(this));
        _hashState('#step1');
        $('#step1Panel').show();
        $('#formPanel').hide();
        $('#step2Panel').hide();
        $('.step2Panel').hide();
        $('#formStep2Panel').hide();
        $('.orders-consultants').show();
        $('.rega-field').hide();
        $('.orders-back-hotels').hide();
        $('#form-fullPanel').hide();
        $('#step3Panel').hide();
        $('#step0Panel').hide();
    });
    $('#form').click(function () {
        line($(this));
        _hashState('#form');
        $('#step1Panel').hide();
        $('#formPanel').show();
        $('#step2Panel').hide();
        $('.step2Panel').hide();
        $('#formStep2Panel').hide();

        $('.orders-consultants').hide();
        $('.orders-back-hotels').hide();
        $('#form-fullPanel').hide();
        $('#step3Panel').hide();
        $('#step0Panel').hide();
    });
    $('#formStep2').click(function () {
        line($(this));
        _hashState('#formStep2');
        $('#step1Panel').hide();
        $('#formPanel').hide();
        $('#step2Panel').hide();
        $('.step2Panel').hide();
        $('#formStep2Panel').show();
        $('.orders-back-hotels').show();
        $('.orders-consultants').hide();
        $('#form-fullPanel').hide();
        $('#step3Panel').hide();
        $('#step0Panel').hide();
    });
    $('#step2').click(function () {
        line($(this));
        _hashState('#step2');
        $('#step1Panel').hide();

        $('#formPanel').hide();
        $('.orders-back-hotels').hide();
        $('#formStep2Panel').hide();
        $('#step2Panel').show();
        $('.step2Panel').show();

        $('.orders-consultants').hide();
        $('#form-fullPanel').hide();
        $('#step3Panel').hide();
        $('#step0Panel').hide();

    });
    $('#form-full').click(function () {
        line($(this));
        _hashState('#form-full');
        $('#step1Panel').hide();

        $('#formPanel').hide();
        $('.orders-back-hotels').hide();
        $('#formStep2Panel').hide();
        $('#step2Panel').hide();
        $('.step2Panel').hide();
        $('.orders-consultants').show();
        $('#form-fullPanel').show();
        $('#step3Panel').hide();
        $('#step0Panel').hide();
    });
    $('#step3').click(function () {
        line($(this));
        _hashState('#step3');
        $('#step1Panel').hide();

        $('#formPanel').hide();
        $('.orders-back-hotels').hide();
        $('#formStep2Panel').hide();
        $('#step2Panel').hide();
        $('.step2Panel').hide();
        $('.orders-consultants').show();
        $('#form-fullPanel').hide();
        $('#step3Panel').show();
        $('#step0Panel').hide();
    });
    $('#step0').click(function () {
        line($(this));
        _hashState('#step0');
        $('#step1Panel').hide();

        $('#formPanel').hide();
        $('.orders-back-hotels').hide();
        $('#formStep2Panel').hide();
        $('#step2Panel').hide();
        $('.step2Panel').hide();
        $('.orders-consultants').show();
        $('#form-fullPanel').hide();
        $('#step3Panel').hide();
        $('#step0Panel').show();
    });
    var line = function (obj) {
        var w = obj.width();
        var p = obj.position().left;
        var el = $('.line ');
        $('.tab').removeClass('active');
        obj.addClass('active');
        el.clearQueue().animate({
            left: p,
            width: w
        }, 300);
    };

    var _hashState = function (_hash) {
        if (history.pushState) {
            history.pushState(null, null, _hash);
        }
        else {
            location.hash = _hash;
        }
    };


    if (!window.location.hash)
        $('.tab.active').first().click();
    else
        $(window.location.hash).click();

    $(window).bind('hashchange', function () {
        $(window.location.hash).click();
    });
    $('.js-type2').on('click', function () {
        $('.js-types-search-tours-blocks').hide();
        $('.js-types-search-hotel-blocks').show();
    });
    $('.js-type1').on('click', function () {
        $('.js-types-search-tours-blocks').show();
        $('.js-types-search-hotel-blocks').hide();
    });


//Добавляем и удаляем контролы
    $('.js-add-field').on('click', function () {
        $('.js-show-added-field').show();
    });
    $('.js-del-field').on('click', function () {
        $('.js-show-added-field').hide();
    });

    $('.js-add-hotel ').on('click', function () {
        $('.js-show-add-hotel').show();
    });

    $('.js-del-hotel ').on('click', function () {
        $('.js-show-add-hotel').hide();
    });


//Направление города
    var sumoDirectionCity= $('select[id="sumo-direction-city"]');
    sumoDirectionCity.SumoSelect({
        search: true,
        forceCustomRendering: true
    });
    sumoDirectionCity.parent().addClass('open');
    sumoDirectionCity.next().next().css('top', '0').css('position', 'relative');


//Направление
    var sumoDirection= $('select[id="sumo-direction"]');
    sumoDirection.SumoSelect({
        search: true,
        forceCustomRendering: true
    });
    sumoDirection.parent().addClass('open');
    sumoDirection.next().next().css('top', '0').css('position', 'relative');


//Список городов вылета
    var sumoListCity = $('select[id="sumo-list-city"]');
    sumoListCity.SumoSelect({
        search: true,
        forceCustomRendering: true
    });
    sumoListCity.parent().addClass('open');
    sumoListCity.next().next().css('top', '0').css('position', 'relative');


//Город вылета
    var sumoDepartment = $('select[id="sumo-department"]');
    sumoDepartment.SumoSelect({
        search: true,
        forceCustomRendering: true
    });
    sumoDepartment.parent().addClass('open');
    sumoDepartment.next().next().css('top', '0').css('position', 'relative');

    var formDateHelp1 = new mytour.searchTours.formDate({
        pickerBlockId: 'js-mt-filter-dtHelp1',
        popupBlockId: 'mtIdxFormDatePPHelp1',
        popupBlock: $('#mtIdxFormDatePPHelp1'),
        datepicker: $('#mtIdxDateHelp1')
    }, mytour.searchTours.main.request);


    var formDateHelp2 = new mytour.searchTours.formDate({
        pickerBlockId: 'js-mt-filter-dtHelp2',
        popupBlockId: 'mtIdxFormDatePPHelp2',
        popupBlock: $('#mtIdxFormDatePPHelp2'),
        datepicker: $('#mtIdxDateHelp2')
    }, mytour.searchTours.main.request);


})
;


