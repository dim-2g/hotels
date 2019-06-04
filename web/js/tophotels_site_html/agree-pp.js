$(function () {

    $('#agreement').click(function () {
        line($(this));
        _hashState('#agreement');
        $('#agreementPanel').show();
        $('#siteRolePanel').hide();
    });
    $('#siteRole').click(function () {
        line($(this));
        _hashState('#siteRole');
        $('#agreementPanel').hide();
        $('#siteRolePanel').show();
    });


    var line = function (obj) {
        var w = obj.width();
        var p = obj.position().left;
        var el = $('.agreement-pp__line');
        $('.agreement-pp__tab').removeClass('active');
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
        $('.agreement-pp__tab.active').first().click();
    else
        $(window.location.hash).click();
    $(window).bind('hashchange', function () {
        $(window.location.hash).click();
    });

    $('.p-agreement-pp').magnificPopup({
        type: 'inline',
        preloader: false,
        focus: '#username',
        modal: true
    });
    $('.p-agreement-pp').on('click', function () {
        $('html').css('overflow', 'hidden');
    });
    $('.legal-information-pp').magnificPopup({
        type: 'inline',
        preloader: false,
        focus: '#username',
        modal: true
    });
    $('.legal-information-pp').on('click', function () {
        $('html').css('overflow', 'hidden');
    });

    $('.p-agreement-pp.agree').on('click', function () {
        $('#agreement').click()
    });

    $('.p-agreement-pp.site-role').on('click', function () {
        $('#siteRole').click();
    });

});