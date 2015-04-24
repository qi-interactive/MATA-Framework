<?php 

namespace mata\db;

use mata\base\DocumentId;
use ReflectionClass;

class ActiveRecord extends \yii\db\ActiveRecord {

	private $additionalProperties = [];

	public function getTopError() {
		if ($this->hasErrors()) {
			$errors = $this->getErrors();
			return current(current($errors));
		}
	}

	public function getDocumentId($attribute = null, $property = null) {

	    $pk = $this->primaryKey;

	    if (is_array($pk))
	        $pk = implode('-', $pk);            

	    if ($attribute != null)
	        $pk .= "::" . $attribute;

	    if ($property != null)
			$pk .= "::" . $property;

	    return new DocumentId(sprintf("%s-%s", get_class($this), $pk));
	}

	public function addAdditionalAttribute($name)
    {
    	array_push($this->additionalProperties, $name);
    }

    public function attributes()
    {
        return array_merge($this->additionalProperties, array_keys(static::getTableSchema()->columns));
    }


}