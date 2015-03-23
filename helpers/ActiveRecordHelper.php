<?php

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