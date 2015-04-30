<?php

namespace mata\widgets\DateTimePicker;

use yii\web\AssetBundle;

/**
 * Class DateTimePickerAsset
 *
 */
class DateTimePickerAsset extends AssetBundle
{
    public $sourcePath = '@vendor/mata/mata-framework/widgets/DateTimePicker/assets';
    public $js = [
        'js/bootstrap-datetimepicker.js'
    ];
    public $css = [
        'css/bootstrap-datetimepicker.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'mata\assets\MomentAsset',
        'mata\widgets\DateTimePicker\DateTimePickerBootstrapAsset',
    ];
}