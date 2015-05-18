<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\helpers;

class BehaviorHelper
{
	public static function hasBehavior($model, $class) {
		foreach ($model->getBehaviors() as $behavior) {
			if (is_a($behavior, $class))
				return true;
		}
		return false;
	}	
}
