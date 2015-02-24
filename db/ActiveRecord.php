<?php 

namespace mata\db;

class ActiveRecord extends \yii\db\ActiveRecord {

	public function getTopError() {
		if ($this->hasErrors()) {
		    $errors = $this->getErrors();
		    return current(current($errors));
		}
	}
}