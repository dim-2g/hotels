$(document).ready(function () {


    $('.left-menu-1023__top-close').on('click', function () {
        $(this).closest('.page').find('.leftbar').hide();
        $(this).closest('.page').find('.content').show();
        $(this).closest('.page').find('.footer').show();
        $(this).closest('.page').find('.leftbar-close').hide();
        $(this).closest('.page').find('.tour-selection-box').show();
        $(this).closest('.page').find('.registration-form').hide();
        $(this).closest('.page').find('.js-show-search').removeClass('active');
        $(this).closest('.page').find('.suggest-big').hide();
        $(this).closest('.page').find('.js-serach-active-hide').show();
        $(this).closest('.page').find('.headerMobile').show();
        $('body,html').animate({scrollTop: 0}, 1);
    });


    var hRegLB = $('.js-observe-scroll').offset().top;
    $(window).scroll(function () {
        if ($(this).scrollTop() > hRegLB) {
            $('.left-menu-1023__top').addClass('fixed-active');
        } else {
            $('.left-menu-1023__top').removeClass('fixed-active');
        }
    });


    $('.js-left-menu-1023-anchor').on('click', function (event) {
        event.preventDefault();
        var id = $(this).attr('href'),
            top = $(id).offset().top - 100;
        $('body,html').animate({scrollTop: top}, 300);
    });
    $('.left-menu-1023 .side-nav-li').on('click', function () {
        $(this).find('.side-nav-li__tabs-list').toggle();
        $(this).toggleClass('rotate')
    });
    $('.header-nav-link ').on('click', function () {
        $(this).find('.side-nav-li__tabs-list').toggle();
    });


    /*новое левое меню на адаптиве*/
    var leftMenuMobile = $(".left-nav-mobile__ul a[href='" + location.pathname + "']").closest("ul");
    $(leftMenuMobile).show();

    $('.left-nav-mobile__li ').on('click', function () {
        $(this).closest('.left-nav-mobile__ul').siblings().find('.left-nav-mobile__li:not(.active)').removeClass('block');
        $(this).closest('.left-nav-mobile__ul').siblings().find('.left-nav-mobile__li.active').removeClass('rotate');
        $(this).closest('.left-nav-mobile__ul').find('.left-nav-mobile__li:not(.active)').toggleClass('block');
        $(this).toggleClass('rotate');
    });


    $('.left-nav-mobile__li a.js-act-nav-link[href="' + location.pathname + '"]').closest('li').addClass('active');


    $('.left-nav-mobile__li.active a').on('click', function () {
        return false
    });

    $('.left-nav-mobile__ul').on('click', function () {
        $('.header-nav-item:not(.active)').hide();
        $('.header-nav-link').removeClass('rotate');
        $('.headerMobile__select-drop').hide();
        $('.headerMobile__select-arr').removeClass('active');
    });






});
