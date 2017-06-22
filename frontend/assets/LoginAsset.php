<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class LoginAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'style/base.css',
        'style/global.css',
        'style/header.css',
        'style/login.css',
        'style/footer.css',
        'style/index.css',
        'style/bottomnav.css',
        'style/home.css',
        'style/address.css',
        'style/goods.css',
        'style/common.css',
        'style/jqzoom.css',
        'style/list.css',
    ];
    public $js = [
        'js/header.js',
        'js/index.js',
        'js/home.js',
        'js/goods.js',
        'js/list.js',
        'js/jqzoom-core.js',
        'js/cart1.js',
        'js/cart2.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
