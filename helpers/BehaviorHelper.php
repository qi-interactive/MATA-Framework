<?php

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