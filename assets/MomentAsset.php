<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\assets;

use yii\web\AssetBundle;

/**
 * Class MomentAsset
 */
class MomentAsset extends AssetBundle
{
    public $sourcePath = '@vendor/bower/moment';

    public $js = [
        'min/moment-with-locales.min.js'
    ];
}
