jQuery(document).ready(function () {

    /*
    $('.bth__loader').on('click', function () {
        $(this).addClass('bth__loader--animate');
    });
    $('button, .bth__btn').on('click', function () {
        $('.js-add-error').addClass('has-error');
        $('.bth__inp-block-eye').hide();
    });
    */
//Подсказки для полей
    $('.bth__inp.js-stop-label').on('focus', function () {
        $(this).addClass('focus');
        $(this).next('.bth__inp-lbl').hide();
    });


    $('.bth__inp.js-stop-label').on('blur', function () {
        if ($(this).val().trim() !== '') {
            $(this).next('.bth__inp-lbl').hide();
        } else {

            $(this).next('.bth__inp-lbl').show();
        }
    });

    $('.js-label').on('focus', function () {
        $(this).next('.bth__inp-lbl').addClass('active');
        $(this).closest('.js-show-saggest').next().show();
    });

    $('.js-label').on('blur', function () {
        if ($(this).val().trim() === '') {
            $(this).next('.bth__inp-lbl').removeClass('active');
            $(this).closest('.js-show-saggest').next().hide();
        }
    });


    $('.js-label').on('change', function () {
        $('.js-label').each(function () {
            if ($(this).val().length) {
                $(this).next('.bth__inp-lbl').addClass('active');
            }
        });
    });

    $('.js-label').change();


    $('.bth__inp-block.long textarea').on('focus', function () {
        $(this).closest('.bth__inp-block.long').addClass('active');
    });
    $('.bth__inp-block.long textarea').on('blur', function () {
        $(this).closest('.bth__inp-block.long').removeClass('active');
    });

    $('.link-disabled').on('click', function (e) {
        e.preventDefault();
    })


});

$(document).on('click', '.js-modal-close', function (e) {
    e.preventDefault();
    $.magnificPopup.close();
});
