<?php 

namespace mata\db;

class ActiveRecord extends \yii\db\ActiveRecord {

	public function getTopError() {
		if ($this->hasErrors()) {
			$errors = $this->getErrors();
			return current(current($errors));
		}
	}

	public function getDocumentId($attribute = null) {

		$pk = $this->primaryKey;

		if (is_array($pk))
			$pk = implode('-', $pk);        	

		if ($attribute != null)
			$pk .= "::" . $attribute;

		return sprintf("%s-%s", get_class($this), $pk);
	}
}