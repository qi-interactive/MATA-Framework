<?php 

namespace mata\db;

class ActiveRecord extends \yii\db\ActiveRecord {

	public function getTopError() {
		if ($this->hasErrors()) {
		    $errors = $this->getErrors();
		    return current(current($errors));
		}
	}

	public function getDocumentId() {

	    $pk = $this->primaryKey;

	    if (is_array($pk))
	        $pk = implode('-', $pk);

	    return sprintf("%s-%s", get_class($this), $pk);
	}
}