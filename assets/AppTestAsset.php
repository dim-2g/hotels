<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppTestAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '/css/_lsfw/reset-ls.css',
        '/css/_lsfw/atom.css',
        '/css/_lsfw/fonts.css',
        '/css/_lsfw/tabs.css',
        '/css/vendor/font-awesome-5.0/css/fontawesome-all.css',
        '/css/vendor/sumoselect.css',
        '/css/vendor/th-sumoselect.css',
        '/css/vendor/magnific-popup.css',

        '/css/tophotels_site_html/main-cnt.css',
        '/css/tophotels_site_html/main.css',
        '/css/tophotels_site_html/layouts/header.css',
        '/css/tophotels_site_html/layouts/header-mobile.css',
        '/css/tophotels_site_html/layouts/footer.css',
        '/css/tophotels_site_html/layouts/left-menu.css',
        '/css/tophotels_site_html/layouts/left-menu-mobile.css',
        '/css/tophotels_site_html/agree-pp.css',
        '/css/tophotels_site_html/tabs-bar-mobile.css',
        '/css/site.css',
    ];
    public $js = [
        '/js/jquery.311.min.js',

    ];
    public $depends = [
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];
}
