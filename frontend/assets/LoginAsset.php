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
    ];
    public $js = [
        'js/header.js',
        'js/index.js',
        'js/home.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
