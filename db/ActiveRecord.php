<?php 

namespace mata\db;

use mata\base\DocumentId;

class ActiveRecord extends \yii\db\ActiveRecord {

	public function getTopError() {
		if ($this->hasErrors()) {
			$errors = $this->getErrors();
			return current(current($errors));
		}
	}

	public function __get($name) {
	     if ($name == "DocumentId")
	         return new DocumentId($this->getAttribute("DocumentId"));

	     return parent::__get($name);
	 }

	 public function getDocumentId($attribute = null) {

	     $pk = $this->primaryKey;

	     if (is_array($pk))
	         $pk = implode('-', $pk);            

	     if ($attribute != null)
	         $pk .= "::" . $attribute;

	     return new DocumentId(sprintf("%s-%s", get_class($this), $pk));
	 }


}