<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\web;

use yii\web\Application as BaseApplication;

class Application extends BaseApplication {

	protected function bootstrap() {
	    $request = $this->getRequest();
	    parent::bootstrap();

	    \Yii::setAlias('@webroot', dirname($request->getScriptFile())  . DIRECTORY_SEPARATOR . "web");
	    \Yii::setAlias('@web', $request->getBaseUrl() . "/web");
	}
}
