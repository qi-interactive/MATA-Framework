<?php

namespace mata\behaviors;

use yii\base\Model;

class IncrementalBehavior extends \yii\base\Behavior {

	public $incrementBy = 1;

	public $initialIncrement = 1;

	public $findBy;

	public $incrementField;

	public function events() {
		return [
			Model::EVENT_BEFORE_VALIDATE => "setIncrement"
		];
	}

	public function setIncrement() {
		$model = $this->owner;

		$findBy = $this->findBy;
		$incrementField = $this->incrementField;

		$lastIncrement = $this->owner->find()->select(sprintf("MAX(`%s`)", $this->incrementField))->where([
			$findBy => $this->owner->$findBy
			])->scalar();

		$this->owner->$incrementField = $lastIncrement != null ? 
			$lastIncrement + $this->incrementBy : $this->initialIncrement;
	}

}