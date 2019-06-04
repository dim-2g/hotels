$(document).ready(function () {

    $('.header-lang__block').on('click', function () {
        $('.header-lang__dropdown').toggleClass('active');
        $('.header-lang__arr ').toggleClass('active');
    });

    $('.header-lang .header-lang__dropdown').on('click', function () {
        $(this).removeClass('active');
        $('.header-lang__arr ').removeClass('active');
    });

    $('.header-lang__lang').on('click', function () {
        var def = $(this).closest('.headerMobile').find('.header-lang__cnt').text();
        var selected = $(this).text();
        $(this).closest('.headerMobile').find('.header-lang__cnt').text(selected);
        $(this).text(def)
    });
    $('.js-show-searchDesct').on('click', function () {
        $(this).toggleClass('active');
        $(this).closest('.headerYellow-line__bubbles').find('.headerYellow-line__bubble-search').toggleClass('active');
        $(this).closest('.headerYellow-line__bubbles').find('.js-hidden').toggle()
    });


    $('.headerMobile__select').on('click', function () {
        $(this).find('.headerMobile__select-drop').toggle();
        $(this).find('.headerMobile__select-arr').toggleClass('active');
        $('.tab:not(.active)').hide().removeClass('toggle-arr');
        $('.header-nav-item:not(.active)').hide();
        $('.header-nav-link').removeClass('rotate');
        $('.left-nav-mobile__li.active').removeClass('rotate');
    });

    $('.headerMobile__select-drop-li a').on('click', function () {
        $('.headerMobile__select-arr').removeClass('active');
        $('.headerMobile__select-drop').hide();

    });

    var countSearch = 0;
    $('.headerMobile__burger').on('click', function () {
        $(this).closest('.page').find('.leftbar').show().css("top", "0");
        $(this).closest('.page').find('.leftbar-inn').addClass('hidden');
        $(this).closest('.page').find('.content').hide();
        $(this).closest('.page').find('.tour-selection-box').hide();
        $(this).closest('.page').find('.footer').hide();
        $(this).closest('.page').find('.left-menu-1023').show();

        $(this).closest('.page').find('.headerMobile').hide();

    });


    $('.headerMobile__bth--auth').on('click', function () {
        $(this).closest('.page').find('.leftbar').hide();
        $(this).addClass('active');
        $(this).closest('.page').find('.container').hide();
        $(this).closest('.page').find('.header').hide();
        $(this).closest('.page').find('.tour-selection-box').hide();
        $(this).closest('.page').find('.footer').hide();


        $(this).closest('.page').find('.header-lang').hide();
        $(this).closest('.page').find('.headerMobile__cross').show();
        $('.headerMobile__registration').show();
    });
    $('.headerMobile__cross ').on('click', function () {
        $(this).closest('.page').find('.leftbar').hide();
        $(this).closest('.page').find('.container').show();
        $(this).closest('.page').find('.header').show();
        $(this).closest('.page').find('.tour-selection-box').show();
        $(this).closest('.page').find('.footer').show();
        $('.headerMobile__bth--auth').removeClass('active')
        $(this).closest('.page').find('.header-lang').show();
        $(this).closest('.page').find('.headerMobile__cross').hide();
        $(this).closest('.page').find('.headerMobile__bth--auth').hide();
        $(this).closest('.page').find('.headerMobile__user').show();
        $('.headerMobile__registration').hide();
    });
    $('.js-sb-1023-close').on('click', function () {
        $(this).closest('.page').find('.leftbar').hide();
        $(this).closest('.page').find('.content').show();
        $(this).closest('.page').find('.tour-selection-box').hide();
        $(this).closest('.page').find('.footer').hide();

        $(this).closest('.page').find('.headerMobile').show();
    });


    $('.js-show-auth-link').on('click', function () {
        $(this).closest('header').find('.header-top-nav-list li:not(:first-of-type)').hide();
        $(this).closest('header').find('.header-top-nav-list li:first-of-type').show();
        $(this).hide()
    });
    $('.js-show-auth').on('click', function () {
        $(this).prev().show();
        $(this).hide()
    });
    $('.js-show-key-block').on('click', function () {
        $(this).prev().show();
        $(this).hide();
        $('.headerMobile__right-auth').hide();
        $('.headerMobile__right-noAuth').show()

    });
    $('.headerMobile__registration-eye').on('click', function () {
        $(this).toggleClass('active');
    });


    //авторизация
    $('#authorization').click(function () {
        line($(this));
        _hashState('#authorization');
        $('#authorizationPanel').show();
        $('#registrationPanel').hide();

        $('#reqLastPanel').hide();
    });

    $('#registration').click(function () {
        line($(this));
        _hashState('#registration');
        $('#authorizationPanel').hide();
        $('#registrationPanel').show();

        $('#reqLastPanel').hide();
    });


    $('#reqLast').click(function () {
        line($(this));
        _hashState('#reqLast');
        $('#authorizationPanel').hide();
        $('#registrationPanel').hide();

        $('#reqLastPanel').show();
    });


    var line = function (obj) {
        var w = obj.width();
        var p = obj.position().left;
        var el = $('.line--reg');
        $('.tab--reg').removeClass('active');
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


    // Верхняя навигация как выпадашка

    var windowWidth = Math.max($(window).width(), window.innerWidth);
    if (windowWidth <= 1023) {


        // При открытии закрыть табы
        $('.header-nav-item.active .header-nav-link').on('click', function () {
            $('.tab:not(.active)').hide().removeClass('toggle-arr');
            $('.headerMobile__select-arr').removeClass('active');
            $('.headerMobile__select-drop').hide();
            $('.left-nav-mobile__li:not(.active)').hide();

            $('.left-nav-mobile__ul').find('.left-nav-mobile__li:not(.active)').removeClass('block');
        });






        var headerActive = $('.header-nav-item.active');
        $('.header-nav-list').prepend(headerActive);


        $('.header-nav-item.active a').on('click', function () {
            $('.header-nav-item:not(.active)').toggle();
            $(this).toggleClass('rotate');
            return false
        });

        // Плавающий блок с табом на адаптиве
        var tabsBarPosition = $('.tabs-bar').offset().top;
        $('.headerMobile__fixed-topline .fa-bars, .headerMobile__fixed-topline span').on('click', function () {

            $('body,html').animate({scrollTop: 0}, 300);
        });
        $('.js-fixed-topline-tab-scroll').on('click', function () {
            $('body,html').animate({scrollTop: 0}, 300);
        });

        $('.js-fixed-topline-open-menu').on('click', function () {
            $('.leftbar').show();
            $('.left-menu-1023').show();
            $('.leftbar-inn').addClass('hidden');
        });
        var hRegLB = $('.panel:visible').offset().top;
        $(window).scroll(function () {
            if ($(this).scrollTop() > hRegLB) {
                $('.headerMobile__fixed-topline').addClass('active');
            } else {
                $('.headerMobile__fixed-topline').removeClass('active');
            }
        });


    }


});

