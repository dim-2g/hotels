"use strict";

var LSPager = function ($block) {

    //
    var self = this;
    var isVisible = false;

    //
    var bid = 'pager_X' + Math.round(Math.random() * 10000 + 1);

    // шаблон пагинатора
    var pagerTemplateRaw =
        '<div id="'+ bid +'" class="pages-nav-block mt40" style="display: none;">' +
        '<a href="#" class="js-pager-doload pages-nav-show-more-btn m10r bold"><span>•••</span>показать ещё</a>' +
        '<ul class="js-pager-pages pages-nav-list pages-nav-list-blue "></ul>' +
        '</div>'
    ;

    // шаблон странички
    var pagerItemTemplateRaw =
        '<li data-page="0" class="js-pager-link pages-nav-list-item">' +
        '<a class="pages-nav-list-link bold">0</a>' +
        '</li>'
    ;

    //
    var pagerItemTemplate = $(pagerItemTemplateRaw);

    // шаблоны в jquery
    $(pagerTemplateRaw).appendTo($block);
    var pagerBlock = $('#' + bid);
    var pagerPages = pagerBlock.find('.js-pager-pages');
    var pagerLinkDoload = pagerBlock.find('.js-pager-doload');
    var pagerLinkSelector = '.js-pager-link';


    /**
     * Создает массив номеров страниц
     * Поддерживает не более 11 кнопок (<5 + текущая + 5>)
     * @param page
     * @param limit
     * @param count
     * @returns {Array}
     */
    this.createPageArray = function(page, limit, count) {
        var pages = Math.ceil(count / limit);
        var pageStart = (((page - 5) > 0) ? page - 4 : 1);
        var pageEnd = (((page + 5) <= pages) ? page + 5 : pages);

        if (page > 5 && (pageEnd - page) < 5) {
            pageStart -= 5 - (pageEnd - page);
        }

        if (count < limit) {
            return [1];
        }

        var result = [1];
        if (pageStart === 1) {
            pageStart = 2;
        }

        for (var $i = pageStart; $i < pageEnd; $i++) {
            result.push($i);
        }

        if (page < 11) {
            for ($i = pageEnd; $i < (pageEnd + (11 - pageEnd)); $i++) {
                if ($i > pages) {
                    break;
                }

                result.push($i);
            }
        }

        if (result.indexOf(pages) === -1){
            result.push(pages);
        }

        return result;
    };

    /**
     * Перезагрузка пагинатора
     * @param page
     * @param limit
     * @param count
     */
    this.reloadPager = function (page, limit, count) {
        // показывать пейджер или нет
        if (count <= limit) {
            if (isVisible) {
                isVisible = false;
                pagerBlock.hide();
            }
            return;
        }
        else
        if (!isVisible) {
            isVisible = true;
            pagerBlock.show();
        }

        // очистка
        var pPages = pagerPages[0];
        while (pPages.firstChild) {
            pPages.removeChild(pPages.firstChild);
        }

        // рендер
        var row;
        var pages = self.createPageArray(page, limit, count);
        for (var i = 0; i < pages.length; i++) {
            row = pagerItemTemplate.clone();
            row.attr('data-page', pages[i]);
            row.children(1).text(pages[i]);

            if (pages[i] === page) {
                row.addClass('pages-nav-list-item_act');
            }

            row.show();
            row.appendTo(pagerPages);
        }
    };

    // Клик на догрузку
    this.bindDoloadClick = function (callback) {
        pagerLinkDoload.click(callback);
    };

    // Клик на страницу
    this.bindPageClick = function (callback) {
        $(document).on('click', pagerLinkSelector, callback);
    };
};
