<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\widgets\DateTimePicker;

use yii\web\AssetBundle;

/**
 * Class DateTimePickerAsset
 */
class DateTimePickerBootstrapAsset extends AssetBundle
{
    public $sourcePath = '@bower/bootstrap/dist';

    public $css = [
        'css/bootstrap.min.css'
    ];

    public $js = [
        'js/bootstrap.js'
    ];
}
