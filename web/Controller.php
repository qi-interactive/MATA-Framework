<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\web;

class Controller extends \yii\web\Controller {
	
	public function setResponseContentType($contentType) {
		header("Content-type: " . $contentType);
	}

}
