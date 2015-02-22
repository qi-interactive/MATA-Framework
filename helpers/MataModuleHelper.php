<?php

namespace mata\helpers;

class MataModuleHelper {

	public static function isMataModule($module) {
		return is_a($module, "mata\base\Module");
	}

	public static function getModuleByClass($class) {
		$modules = \Yii::$app->getModules();

		foreach ($modules as $module) {

			if (is_array($module))
				$module = new $module["class"](null); // module not initialized

			if (get_class($module) == $class)
				return $module;
		}
	}
}
