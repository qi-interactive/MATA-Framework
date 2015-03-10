<?php

namespace mata\web;

class Controller extends \yii\web\Controller {
	
	public function setResponseContentType($contentType) {
		header("Content-type: " . $contentType);
	}

}
