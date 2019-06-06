<?php
/**
 * Для подключения скрптов и стилей внизу страницы
  */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppBottomAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
        '/js/jquery-ui.min.js',
        '/js/vendor/magnific-popup.min.js',
        '/js/vendor/SumoSelectLS/js/jquery.sumoselect-ls.min.js',
        '/js/vendor/jquery-datepicker-range.js',
        '/js/tophotels_site_html/tk-form-v2/date-function.js',
        '/js/tophotels_site_html/tk-form-v2/main.js',
        '/js/tophotels_site_html/tk-form-v2/form-date.js',
        '/js/tophotels_site_html/form-pp-universal.js',
        '/js/tophotels_site_html/form-directions.js',
        '/js/tophotels_site_html/main.js',
        '/js/tophotels_site_html/help-selections.js',
        '/js/tophotels_site_html/agree-pp.js',
        '/js/tophotels_site_html/header-mobile.js',
        '/js/tophotels_site_html/left-menu-mobile.js',
        '/js/tophotels_site_html/legal-info-pp.js',
        '/js/libs/array-function.js',
        '/js/libs/date-function.js',
        '/js/libs/number-function.js',
        '/js/libs/string-function.js',
        '/js/libs/debounce.js',
        '/js/libs/reverseLocale.js',
        '/js/libs/LSPager.js',
        '/js/libs/LSSuggest.js',
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];
}
