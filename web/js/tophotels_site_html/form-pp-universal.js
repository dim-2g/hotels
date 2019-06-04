"use strict";

(function(){
    $(document).on('click', '.js-lsfw-pp', function(){
        $(this).next().addClass('hidden');
    });

    $(document).on('click', '.js-lsfw-ppdb', function(){
        $(this).next().addClass('d-ib');
    });

    $(document).on('click', '.js-lsfw-pp-close', function(){
        $(this).closest('.formDirections').removeClass('d-ib');
    });

    $(document).on('click', '.js-lsfw-ppdb-close', function(){
        $(this).closest('.formDirections').removeClass('d-ib');
    });
})();