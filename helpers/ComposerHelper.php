<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\helpers;

class ComposerHelper {

	public static function getLibraryDirByNamespace($namespace) {

		$includeFiles = require(\Yii::getAlias('@vendor') . DIRECTORY_SEPARATOR . "composer"
			. DIRECTORY_SEPARATOR . "autoload_psr4.php");

		if (array_key_exists($namespace, $includeFiles)) {
			foreach($includeFiles[$namespace] as $autoloadDirectory) {
				if(!file_exists($autoloadDirectory))
					continue;
				return $autoloadDirectory;
			}

		}
	}

	public static function getLibraryNamespaceByDir($dir) {
		$includeFiles = require(\Yii::getAlias('@vendor') . DIRECTORY_SEPARATOR . "composer"
			. DIRECTORY_SEPARATOR . "autoload_psr4.php");

		foreach ($includeFiles as $namespace => $value) {
			if (in_array($dir, $value))
				return $namespace;
		}
	}
}
