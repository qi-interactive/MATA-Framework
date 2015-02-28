<?php 

namespace mata\base;

class MessageEvent extends \yii\base\Event {

    private $message;

    public function __construct($message) {
    	$this->message = $message;
    	parent::__construct([]);
    }

    public function getMessage() {
    	return $this->message;
    }
}