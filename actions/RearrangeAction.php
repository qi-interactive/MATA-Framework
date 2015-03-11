<?php

namespace mata\actions;

use Yii;
use mata\base\ValidationException;

class RearrangeAction extends \yii\base\Action {

	public $model;
	public $onValidationErrorHandler;

	public function init() {
		if(empty($this->onValidationErrorHandler)) {
			$this->onValidationErrorHandler = function($model, $exception) {
				throw $exception;
			};
		}
		
	}

	public function run() {
		// Load data and validate
		try {
			print_r(Yii::$app->request->post());

		} catch (ValidationException $e) {
			call_user_func_array($this->onValidationErrorHandler, [$this->model, $e]);
		}

		echo "OK";
		
	}

}  