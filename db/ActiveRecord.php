<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

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

	public function __get($name) {
	     if ($name == "DocumentId")
	         return new DocumentId($this->getAttribute("DocumentId"));

	     return parent::__get($name);
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
