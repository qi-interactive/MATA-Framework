<?php
/**
 * @author: Harry Tang (giaduy@gmail.com)
 * @link: http://www.greyneuron.com
 * @copyright: Grey Neuron
 */

namespace mata\widgets\fineuploader;

use yii\widgets\InputWidget;
use yii\helpers\Json;
use yii\web\View;
use mata\widgets\fineuploader\FineUploaderAsset;
use mata\keyvalue\models\KeyValue;
use mata\media\models\Media;

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
        'multiple' => true
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

        if(empty($this->uploadSuccessEndpoint))
            $this->uploadSuccessEndpoint = '/mata-cms/media/s3/upload-successful?documentId=' . urlencode($this->model->getDocumentId());

        $this->s3Key = trim(KeyValue::findbyKey(self::S3_KEY));
        $this->s3Secret = trim(KeyValue::findbyKey(self::S3_SECRET));
        $this->s3Bucket = "/" . KeyValue::findbyKey(self::S3_BUCKET);

        $this->s3URL = KeyValue::findbyKey(self::S3_ENDPOINT);
        $this->s3Folder = KeyValue::findbyKey(self::S3_REDACTOR_FOLDER);
    }

    /**
     * @inheritdoc
     */
    public function run() {
        $this->selector = '#' . $this->htmlOptions['id'];

        $this->registerPlugin();
        $this->registerJS();

        echo $this->render($this->view, [
            "widget" => $this,
            "mediaModel" => Media::find()->forItem($this->model, $this->attribute)->one()
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