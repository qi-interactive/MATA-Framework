<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\base;
use yii\base\Module as BaseModule;
use mata\helpers\ComposerHelper;
use yii\base\InvalidConfigException;

abstract class Module extends BaseModule {

	public abstract function getNavigation();
	
	public $mataConfig = [];

	public $canShowInNavigation = true;

	const CONFIG_FILE_NAME = "module-config.json";
	const DEFAULT_ASSET_BUNDLE = "ModuleAsset";

	private $defaultModuleAssetBundle;

	public function __construct($id, $parent = null, $config = []) {
		$config["mataConfig"] = $this->getMataConfig();
		parent::__construct($id, $parent, $config);
	}

	// TODO Cross-reference to matacms, tight coupling!
	public $layout = "@matacms/views/layouts/module"; 

	public function getVersion() {
		return $this->mataConfig->version;
	}

	private function getDefaultConfig() {
		return [
			'class' => get_class($this)
		];
	}

	public function getConfig() {
		return $this->getDefaultConfig();
	}

	public function canShowInNavigation() {
		return $this->canShowInNavigation;
	}

	public function getName() {
		return $this->mataConfig->name;
	}

	public function getDescription() {
		return $this->mataConfig->description;
	}

	public function getIcon() {

		$assetBundle = $this->getModuleAssetBundle();
		$assetBundleInstance = $assetBundle->register(\Yii::$app->view);

		return $assetBundleInstance->sourcePath . $this->mataConfig->icon;
	}

	private function getMataConfig() {
		$classInfo = new \ReflectionClass($this);
		
		$namespace = str_replace("Module", "", $classInfo->name);

		$libraryDir = str_replace("Module.php", "", $classInfo->getFileName());
		$configFile = $libraryDir . DIRECTORY_SEPARATOR . self::CONFIG_FILE_NAME;

		if (file_exists($configFile) == false)
			throw new InvalidConfigException(sprintf("Could not find config file %s %s", self::CONFIG_FILE_NAME, $configFile));

		$config = json_decode(file_get_contents($libraryDir . DIRECTORY_SEPARATOR . self::CONFIG_FILE_NAME));

		if ($config == null)
			throw new InvalidConfigException(sprintf("Could not decode %s", $configFile));

		return $config;
	}

	public function getModuleAssetBundle() {

		if ($this->defaultModuleAssetBundle == null) {

			$classInfo = new \ReflectionClass($this);

			$namespace = str_replace("Module", "", $classInfo->name) . "assets\\"; 
			$moduleAsset = $namespace . self::DEFAULT_ASSET_BUNDLE;

			if (class_exists($moduleAsset) == false)
				return null;

			$this->defaultModuleAssetBundle = new $moduleAsset();
		}

		return $this->defaultModuleAssetBundle;
	}
}
