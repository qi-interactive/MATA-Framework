<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\widgets\fineuploader;

use yii\widgets\InputWidget;
use yii\helpers\Json;
use yii\web\View;
use mata\widgets\fineuploader\FineUploaderAsset;
use mata\keyvalue\models\KeyValue;
use mata\media\models\Media;
use mata\helpers\StringHelper;

class FineUploader extends InputWidget {

    // NOT THE RIGHT TO PLACE THESE - WHERE THOUGH?
    const S3_KEY = "S3_KEY";
    const S3_SECRET = "S3_SECRET";
    const S3_BUCKET = "S3_BUCKET";
    const S3_ENDPOINT = "S3_ENDPOINT";
    const S3_REDACTOR_FOLDER = "S3_REDACTOR_FOLDER";

    public $selector = null;
    /**
     * @var array the html options.
     */
    public $htmlOptions = [];

    public $defaultOptions = [
        'multiple' => true,
        'allowedExtensions' => ['jpg', 'jpeg', 'gif', 'png', 'pdf', 'ico'],
        'sizeLimit' => 2000000
    ];

    public $events = array();
    public $default_events = array(
        'autoRetry'=>'',
        'cancel'=>'',
        'complete'=>'',
        'allComplete'=>'',
        'delete'=>'',
        'deleteComplete'=>'',
        'error'=>'',
        'manualRetry'=>'',
        'pasteReceived'=>'',
        'progress'=>'',
        'resume'=>'',
        'sessionRequestComplete'=>'',
        'statusChange'=>'',
        'submit'=>'',
        'submitDelete'=>'',
        'submitted'=>'',
        'totalProgress'=>'',
        'upload'=>'',
        'uploadChunk'=>'',
        'uploadChunkSuccess'=>'',
        'validate'=>'',
        'validateBatch'=>'',
        );

    public $s3Key;
    public $s3Secret;
    public $s3Bucket;
    public $s3URL;
    public $s3Folder;

    public $uploadSuccessEndpoint;
    public $view = 'fineUploader';

    public function init(){
        parent::init();

        $this->options=array_merge($this->defaultOptions, $this->options);

        $this->events=array_merge($this->default_events, $this->events);

        if (!isset($this->htmlOptions['id'])) {
            $this->htmlOptions['id'] = $this->getId();
        }

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        } else {
            $this->options['id'] = $this->options['id'];
        }

        if(empty($this->uploadSuccessEndpoint))
            $this->uploadSuccessEndpoint = '/mata-cms/media/s3/upload-successful?documentId=' . urlencode($this->model->getDocumentId($this->attribute));

        $this->s3Key = trim(KeyValue::findValue(self::S3_KEY));
        $this->s3Secret = trim(KeyValue::findValue(self::S3_SECRET));
        $this->s3Bucket = "/" . KeyValue::findValue(self::S3_BUCKET);

        $this->s3URL = KeyValue::findValue(self::S3_ENDPOINT);
        $this->s3Folder = KeyValue::findValue(self::S3_REDACTOR_FOLDER);
    }

    public function run() {

        $assetBundle = \Yii::$app->getAssetManager()->getBundle(\mata\widgets\fineuploader\FineUploaderAsset::className());

        $this->options=array_merge($this->options, [
            'thumbnails' => [
                'placeholders' => [
                    'notAvailablePath' => $assetBundle->baseUrl . '/fine-uploader/placeholders/not-image-file.png'
                ]
            ]
        ]);

        $this->selector = '#' . $this->options['id'];

        $this->registerPlugin();
        $this->registerJS();
        $mediaModel = Media::find()->forItem($this->model, $this->attribute)->one();

        /**
         * In case of validation errors, the same form should be returned
         */
        if(!empty($_POST['Media'])) {
            foreach($_POST['Media'] as $key => $media) {
                $documentId = isset($media["DocumentId"]) ? $media["DocumentId"] : null;

                if ($documentId && StringHelper::endsWith($documentId, '::' . $this->attribute)) {
                    $mediaModel = Media::find()->where(['For' => $media])->one();
                }
            }
        }
        echo $this->render($this->view, [
            "widget" => $this,
            "mediaModel" => $mediaModel
            ]);
    }

    /**
     * Registers plugin and the related events
     */
    protected function registerPlugin() {
        $view = $this->getView();
        FineUploaderAsset::register($view);
    }

    /**
     * Register JS
     */
    protected function registerJS() {
        $options = Json::encode($this->options);

    }
}
