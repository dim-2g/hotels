/**
 * Форматирует число в вид 123 123 123
 * @param n
 * @return string
 */
function priceFormat(n) {
    return (n + '').replace(/\D/g, '').split(/(?=(?:\d{3})+$)/).join(' ');
}