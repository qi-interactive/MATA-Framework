<?php

namespace mata\helpers;


class ComposerHelper {

	public static function getLibraryDirByNamespace($namespace) {

		$includeFiles = require(\Yii::getAlias('@vendor') . DIRECTORY_SEPARATOR . "composer"
			. DIRECTORY_SEPARATOR . "autoload_psr4.php");

		if (array_key_exists($namespace, $includeFiles))
			return current($includeFiles[$namespace]);
	}

	public static function getLibraryNamespaceByFolder($folder) {
		$includeFiles = require(\Yii::getAlias('@vendor') . DIRECTORY_SEPARATOR . "composer"
			. DIRECTORY_SEPARATOR . "autoload_psr4.php");

		foreach ($includeFiles as $namespace => $value) {
			if (current($value) == $folder)
				return $namespace;
		}
	}
}