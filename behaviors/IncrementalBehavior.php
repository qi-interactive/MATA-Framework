<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\behaviors;

use yii\base\Model;

class IncrementalBehavior extends \yii\base\Behavior {

	public $incrementBy = 1;

	public $initialIncrement = 1;

	public $findBy;

	public $incrementField;

	/**
	 * [[incrementField]] will not be changed if it's already set (not NULL);
	 * Set this property to true to overwrite the existing value
	 */ 
	public $forceIncrement = false;

	public function events() {
		return [
			Model::EVENT_BEFORE_VALIDATE => "setIncrement"
		];
	}

	public function setIncrement() {

		
		$model = $this->owner;

		$findBy = $this->findBy;
		$incrementField = $this->incrementField;

		if ($this->owner->$incrementField == null || 
			$this->forceIncrement) {
			
		$lastIncrement = $this->owner->find()->select(sprintf("MAX(`%s`)", $this->incrementField))->where([
			$findBy => $this->owner->$findBy
			])->scalar();

		$this->owner->$incrementField = $lastIncrement != null ? 
			$lastIncrement + $this->incrementBy : $this->initialIncrement;
		}
	}
}
