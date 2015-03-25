<?php

namespace mata\helpers;

use mata\helpers\ComposerHelper;

class MataModuleHelper {

	public static function isMataModule($module) {
		return is_a($module, "mata\base\Module");
	}

	public static function getModuleByClass($class) {
		$modules = \Yii::$app->getModules();

		foreach ($modules as $id => $module) {

			if (is_array($module))
				$module = new $module["class"]($id); // module not initialized

			if (get_class($module) == $class)
				return $module;
		}
	}

	public static function getModuleNamespaceByDir($dir) {
		$moduleFile = ComposerHelper::getLibraryNamespaceByDir($dir);

		if ($moduleFile == null) {

			$dir =  $dir . DIRECTORY_SEPARATOR . "Module.php";
			$modules = \Yii::$app->getModules();

			foreach ($modules as $module) {

				if (is_array($module))
					$module = self::getModuleByClass($module["class"]);

				$reflector = new \ReflectionClass($module);

				if ($reflector->getFileName() == $dir) {
					return $reflector->getNamespaceName() . "\\";
				}
			}
		}	

		return $moduleFile;	
	}
}
