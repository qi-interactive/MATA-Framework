<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\widgets\fineuploader;

use yii\web\AssetBundle;

class FineUploaderAsset extends AssetBundle {

    public $sourcePath = '@mata/widgets/fineuploader/assets';
    public $js = [
        // 'jquery.fine-uploader/jquery.fine-uploader.min.js',
    's3.jquery.fine-uploader/s3.jquery.fine-uploader.min.js'
    ];
    public $css = [
        'jquery.fine-uploader/fine-uploader.min.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
