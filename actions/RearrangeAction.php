<?php

namespace mata\actions;

use Yii;
use mata\base\ValidationException;
use yii\helpers\Json;

class RearrangeAction extends \yii\base\Action {

	public $model;
	public $onValidationErrorHandler;
	public $modalTitle;
	public $orderColumnName;
	public $url;

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
			$data = Yii::$app->request->post();
			$pks = $data['pks'];
			$orderColumnName = $this->orderColumnName;

			foreach($pks as $index => $pk) {
				$model = $this->model->findOne($pk);
				$model->$orderColumnName = $index+1;
				if(!$model->save())
					throw new NotFoundHttpException($model->getTopError());
			}
			echo Json::encode(['Response' => 'OK']);
		} catch (NotFoundHttpException $e) {
			call_user_func_array($this->onValidationErrorHandler, [$this->model, $e]);
			return;
		}
		
	}

}  