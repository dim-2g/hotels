/**
 * Created by MaksymZ on 14.07.2017.
 */

/**
 * Разница двух массивов
 * @param {Array} a2
 * @returns {Array.<*>}
 */
Array.prototype.diff = function(a2) {
    var diff;
    diff = this.filter(function(i) {return a2.indexOf(i) < 0;});
    diff = diff.concat(a2.filter(function(i) {return this.indexOf(i) < 0;}));

    return diff.filter(function(value, index, self){ return self.indexOf(value) === index; });
};

/**
 * Разница двух массивов (только уник элементы)
 * @param {Array} a2
 * @returns {Array.<*>}
 */
Array.prototype.diffUnique = function(a2) {
    return this.diff(a2).unique();
};

/**
 * Массивы одинаковы?
 * @param {Array} a2
 * @returns {boolean}
 */
Array.prototype.equals = function(a2) {
    return this.diff(a2).length === 0;
};

/**
 * Массив включает массив а2?
 * @param {Array} a2
 * @returns {boolean}
 */
Array.prototype.contains = function(a2) {
    return (
        this.filter(function (elem) { return a2.indexOf(elem) > -1;}).length === a2.length
    );
};

/**
 * Создает новый массив уникальных элементов
 * @returns {Array.<*>}
 */
Array.prototype.unique = function() {
    return this.filter(function (value, index, self) {
        return self.indexOf(value) === index;
    });
};

/**
 * Пересечения массивов
 * @param {Array} a2
 * @returns {Array.<*>}
 */
Array.prototype.intersect = function(a2) {
    return this.filter(function(n) {
        return a2.indexOf(n) !== -1;
    });
};

/**
 * Уникальные пересечения массивов
 * @param {Array} a2
 * @returns {Array.<*>}
 */
Array.prototype.intersectUnique = function(a2) {
    return this.intersect(a2).unique();
};