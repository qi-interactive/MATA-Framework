<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\helpers;

class ActiveRecordHelper
{
	public static function getPk($model)
	{
		$pk = $model->primaryKey;

		if (is_array($pk))
			$pk = implode('-', $pk); 

		return $pk;
	}
}
