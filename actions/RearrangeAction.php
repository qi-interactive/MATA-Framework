<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\actions;

use Yii;
use mata\base\ValidationException;
use mata\helpers\BehaviorHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

class RearrangeAction extends \yii\base\Action {

	public $model;
	public $onValidationErrorHandler;
	public $modalTitle;
	public $orderColumnName;
	public $url;
	public $groupingField = null;

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
			if(!BehaviorHelper::hasBehavior($this->model, \mata\behaviors\ItemOrderableBehavior::class))
				throw new NotFoundHttpException(get_class($this->model) . ' does not have ItemOrderableBehavior');

			$data = Yii::$app->request->post();
			$pks = $data['pks'];
			$orderColumnName = $this->orderColumnName;

			foreach($pks as $index => $pk) {
				$model = $this->model->findOne($pk);
				if($this->groupingField != null)
					$model->groupingField = $this->groupingField;
				$model->applyOrder($index+1);
			}

			echo Json::encode(['Response' => 'OK']);
			
		} catch (NotFoundHttpException $e) {
			call_user_func_array($this->onValidationErrorHandler, [$this->model, $e]);
			return;
		}
	}
}  
