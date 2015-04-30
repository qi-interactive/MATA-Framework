<?php

namespace mata\assets;

use yii\web\AssetBundle;

/**
 * Class MomentAsset
 *
 */
class MomentAsset extends AssetBundle
{
    public $sourcePath = '@vendor/bower/moment';

    public $js = [
        'min/moment-with-locales.min.js'
    ];

}