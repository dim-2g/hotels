var ____stripScripts_regexCache_0xMdf = /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi;

/**
 * Обрезать <script> теги
 * @param s
 */
var stripScripts = function(s) {
    return s.replace(____stripScripts_regexCache_0xMdf, '');
};