/**
 * Объект переводит строки из одной раскладки в другую
 *
 * @type {{ruKeyboard: [string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string], enKeyboard: [string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string,string], replace: reverseLocale.replace, convertRuToEn: reverseLocale.convertRuToEn, convertEnToRu: reverseLocale.convertEnToRu}}
 */
var reverseLocale = {
    /** @var array */
    ruKeyboard: [
        "й","ц","у","к","е","н","г","ш","щ","з","х","ъ",
        "ф","ы","в","а","п","р","о","л","д","ж","э",
        "я","ч","с","м","и","т","ь","б","ю",
        "х","ъ","ж","э","б","ю","\\."
    ],

    /** @var array */
    enKeyboard: [
        "q","w","e","r","t","y","u","i","o","p","\\[","\\]",
        "a","s","d","f","g","h","j","k","l",";","'",
        "z","x","c","v","b","n","m",",","\\.",
        "\\{","\\}",":","\"","\\<","\\>","\\?"
    ],

    /**
     * @param str
     * @param from
     * @param to
     */
    replace: function (str, from, to) {
        var reg;
        str = str.toLocaleLowerCase();

        for (var i = 0; i < from.length; i++) {
            reg = new RegExp(from[i], 'mig');

            str = str.replace(reg, function (a) {
                return to[i];
            });
        }

        return str;
    },

    /**
     * Меняет раскладку текста с русской на английскую
     * @param str
     */
    convertRuToEn: function(str) {
        return reverseLocale.replace(str, reverseLocale.ruKeyboard, reverseLocale.enKeyboard);
    },

    /**
     * Меняет раскладку текста с англ на русскую
     * @param str
     */
    convertEnToRu: function(str) {
        return reverseLocale.replace(str, reverseLocale.enKeyboard, reverseLocale.ruKeyboard);
    }
};