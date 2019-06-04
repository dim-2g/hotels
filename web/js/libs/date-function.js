/**
 * Created by MaksymZ on 18.10.2016.
 */

var dateLocaleWeekDaysShort = {
    1: 'пн',
    2: 'вт',
    3: 'ср',
    4: 'чт',
    5: 'пт',
    6: 'сб',
    0: 'вс'
};

var dateLocaleWeekDays = {
    1: 'понедельник',
    2: 'вторник',
    3: 'среда',
    4: 'четверг',
    5: 'пятница',
    6: 'суббота',
    0: 'воскресенье'
};

var dateLocaleMonthShort = {
    0: 'янв',
    1: 'фев',
    2: 'мар',
    3: 'апр',
    4: 'май',
    5: 'июн',
    6: 'июл',
    7: 'авг',
    8: 'сен',
    9: 'окт',
    10: 'ноя',
    11: 'дек',
};

var dateLocaleMonth = {
    0: 'январь',
    1: 'февраль',
    2: 'март',
    3: 'апрель',
    4: 'май',
    5: 'июнь',
    6: 'июль',
    7: 'август',
    8: 'сентябрь',
    9: 'октябрь',
    10: 'ноябрь',
    11: 'декабрь',
};

var dateLocaleMonthWhen = {
    0: 'января',
    1: 'февраля',
    2: 'марта',
    3: 'апреля',
    4: 'мая',
    5: 'июня',
    6: 'июля',
    7: 'августа',
    8: 'сентября',
    9: 'октября',
    10: 'ноября',
    11: 'декабря',
};

/**
 * Возвращает unix time для текущей даты
 * @returns {number}
 */
Date.prototype.unixTime = function() {
    return Math.round(this.getTime() / 1000);
};

/**
 * Определяет что дата bd находится перед текущей датой
 * @param bd
 * @returns {boolean}
 */
Date.prototype.before = function(bd) {
    return bd.getTime() > this.getTime();
};

/**
 * Определяет что дата объекта находится перед сегодняшней датой
 * @returns {boolean}
 */
Date.prototype.beforeToday = function() {
    var d = Date.createFromIsoDate((new Date()).format('Y-m-d'));
    return d.getTime() > this.getTime();
};

/**
 * Определяет что дата ad находится после текущей даты
 * @param ad
 * @returns {boolean}
 */
Date.prototype.after = function(ad) {
    return ad.getTime() < this.getTime();
};

/**
 * Определяет что даты одинаковые
 * @param d
 * @returns {boolean}
 */
Date.prototype.equals = function(d) {
    return d.getTime() === this.getTime();
};

/**
 * Количество дней в месяце
 * От 28 до 31
 * @returns {number}
 */
Date.prototype.monthDays = function(){
    return new Date(this.getFullYear(), this.getMonth() + 1, 0).getDate();
};

/**
 * Создает строку из даты, согласно формату
 * @param format
 * @param noLeadZero
 * @returns {*}
 */
Date.prototype.format = function(format, noLeadZero) {

    if (typeof noLeadZero === 'undefined') {
        noLeadZero = false;
    }

    var f = {
        monWhen : dateLocaleMonthWhen[this.getMonth()],
        monShort : dateLocaleMonthShort[this.getMonth()],
        mon : dateLocaleMonth[this.getMonth()],
        weekDayShort: dateLocaleWeekDaysShort[this.getDay()],
        weekDay: dateLocaleWeekDays[this.getDay()],

        y : (this.getFullYear() + '').substr(2,2),
        Y : this.getFullYear(),
        m : this.getMonth() + 1,
        d : this.getDate(),
        H : this.getHours(),
        i : this.getMinutes(),
        s : this.getSeconds(),
        t : this.monthDays()
    };

    for (var k in f) {
        if (f.hasOwnProperty(k)) {
            if (noLeadZero === true) {
                format = format.replace(k, f[k]);
            }
            else {
                format = format.replace(k, ((f[k] < 10) ? '0' + f[k] : f[k]));
            }
        }
    }

    return format;
};

/**
 * Гарантированно создает дату из dd.mm.yyyy формата
 * @param d
 * @returns {Date}
 */
Date.isISO = function(d) {
    var parts = d.split('-');
    return (parts.length === 3 && parts[0].length === 4 && parts[1].length === 2 && parts[2].length === 2);
};

/**
 * Гарантированно создает дату из dd.mm.yyyy формата
 * @param d
 * @returns {Date}
 */
Date.isDDMMYYYY = function(d) {
    var parts = d.split('.');
    return (parts.length === 3 && parts[0].length === 2 && parts[1].length === 2 && parts[2].length === 4);
};

/**
 * Разница в днях между двумя датами
 * @param d
 * @returns {number}
 */
Date.prototype.dayDiff = function(d) {
    return (this.getTime() - d.getTime()) / 1000 / 60 / 60 / 24;
};

/**
 * Гарантированно создает дату из dd.mm.yyyy формата
 * @param d
 * @returns {Date}
 */
Date.createFromDDMMYYY = function(d) {
    var parts = d.split('.');
    return new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]));
};

/**
 * Гарантированно создает дату из ISO формата
 * @param d
 * @returns {Date}
 */
Date.createFromIsoDate = function(d) {
    var parts = d.split('-');
    return new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
};

/**
 * Гарантированно создает дату из ISO формата
 * Игнорирует TimeZone
 * @param d
 * @returns {Date}
 */
Date.createFromIsoDateWithoutTZ = function(d) {
    var parts = d.split('-');
    var date = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
    return new Date(date.getTime() - (date.getTimezoneOffset() * 60000));
};

/**
 * Гарантированно создает дату из ISO формата
 * Игнорирует TimeZone
 * Прибавляет offset
 * @param d
 * @param offsetMinutes
 * @returns {Date}
 */
Date.createFromIsoDateWithOffset = function(d, offsetMinutes) {
    var parts = d.split('-');
    var date = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
    return new Date(date.getTime() - (offsetMinutes * 60000));
};

/**
 * Сегодняшний день в ISO формате
 * @returns {Date}
 */
Date.todayISO = function() {
    return new Date().format('Y-m-d');
};

/**
 * @param days
 * @returns {Date}
 */
Date.prototype.addDays = function(days) {
    var dat = new Date(this.valueOf())
    dat.setDate(dat.getDate() + days);
    return dat;
};

/**
 * @param value
 * @returns {Date}
 */
Date.prototype.addMonths = function (value) {
    var n = this.getDate();
    this.setDate(1);
    this.setMonth(this.getMonth() + value);
    this.setDate(Math.min(n, this.monthDays()));
    return this;
};

/**
 * @param days
 * @returns {Date}
 */
Date.prototype.minusDays = function(days) {
    var dat = new Date(this.valueOf());
    dat.setDate(dat.getDate() - days);
    return dat;
};

/**
 * @param startDate
 * @param stopDate
 * @returns {Array}
 */
Date.getDatesRange = function(startDate, stopDate) {
    var dateArray = [];
    var currentDate = startDate;

    while (currentDate <= stopDate) {
        dateArray.push(new Date(currentDate));
        currentDate = currentDate.addDays(1);
    }

    return dateArray;
};
