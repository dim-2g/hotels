$(function () {
    $('.legal-information-pp').magnificPopup({
        type: 'inline',
        preloader: false,
        focus: '#username',
        modal: true

    });
    $('.legal-information-pp').on('click', function () {
        $('#usage-role').click()
    });
    $('#usage-role').click(function () {
        line($(this));
        _hashState('#usage-role');
        $('#usage-rolePanel').show();
        $('#confidentialityPanel').hide();
    });
    $('#confidentiality').click(function () {
        line($(this));
        _hashState('#confidentiality');
        $('#usage-rolePanel').hide();
        $('#confidentialityPanel').show();
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
});
