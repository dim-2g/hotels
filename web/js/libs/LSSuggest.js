"use strict";

/**
 * Обертка для саджестов
 * - Прицепляется к input
 * - Можно повесить событие на поиск со своим обработчиком
 * - Можно повесить событие на клик по результату со своим обработчиком
 * - Можно повесить любое событие на любое действие на любой элемент внутри списка результатов
 * - Хранит список значений по id
 * - Хранит список DOM по id
 * @param element
 * @constructor
 */
var LSSuggest = function (element) {
    var self = this;

    //
    this.suggest = $(element);
    this.suggestList = $('<div></div>')
        .addClass('no_focus_highlight')
        .addClass('crm__suggest-items')
        .hide()
        .insertAfter(this.suggest);
    this.items = {};
    this.itemsDOM = {};

    //
    this.actionSearch = null;

    // Очистка списка
    this.clear = function() {
        self.suggestList.html('');
        self.items = {};
        self.itemsDOM = {};
    };

    // Удаление из списка
    this.remove = function(id) {
        var old = self.getItem(id);
        if (typeof old !== 'undefined') {
            old.remove();
        }

        self.items[id] = undefined;
        self.itemsDOM[id] = undefined;
    };

    // Добавление в список
    this.add = function(id, name) {
        self.remove(id);

        self.items[id] = name;
        self.itemsDOM[id] = $('<div></div>')
            .addClass('crm__suggest-item')
            .addClass('js-ls-suggest-item')
            .attr('data-id', id)
            .attr('data-name', name)
            .html(name)
            .appendTo(self.suggestList)
        ;
    };

    // Кастомное Добавление в список
    this.addCustom = function(jqBlock, id, name) {
        self.remove(id);

        self.items[id] = name;
        self.itemsDOM[id] = jqBlock
            .addClass('crm__suggest-item')
            .addClass('js-ls-suggest-item')
            .attr('data-id', id)
            .attr('data-name', name)
            .appendTo(self.suggestList)
        ;
    };

    // Сколько элементов в саджесте
    this.size = function() {
        return Object.keys(self.items).length;
    };

    // Перезадает значение
    // например если мы хотим сохранить объект
    this.set = function (id, val) {
        return self.items[id] = val;
    };

    // Получение значения
    this.get = function (id) {
        return self.items[id];
    };

    // Получение элемента
    this.getItem = function (id) {
        return self.itemsDOM[id];
    };

    // Получение элемента
    this.getFirstItem = function () {
        return self.suggestList.children().get(0);
    };

    // Пользовательская сортировка
    this.sortItemsCustom = function (callback) {
        self.suggestList.find('div').sort(callback).appendTo(self.suggestList);
    };

    // Сортировка по алфавиту
    this.sortItemsAbc = function () {
        var itemA, itemB;
        self.sortItemsCustom(function(a, b) {
            itemA = $(a).attr('data-id');
            itemB = $(b).attr('data-id');
            return self.get(itemA).localeCompare(self.get(itemB));
        });
    };

    //
    this.bindAction = function (action, element, callback) {
        self.suggestList.on(action, element, callback);
    };

    //
    this.bindActionSearch = function (callback) {
        self.actionSearch = callback;
        self.suggest.focus(callback);
        self.suggest.keyup(invDebounce(callback, 250, false));
    };

    //
    this.bindActionItemClick = function (callback) {
        self.bindAction('click', '.js-ls-suggest-item', callback);
    };

    //
    this.showSuggest = function() {
        self.suggestList.show();
    };

    //
    this.hideSuggest = function() {
        self.suggestList.hide();
    };

    //
    this.setText = function(text) {
        self.suggest.val(text);
    };

    //
    this.runSearch = function(afterSearchCallabck) {
        self.actionSearch(afterSearchCallabck);
    };
};