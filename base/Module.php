<?php 

namespace mata\base;
use yii\base\Module as BaseModule;
use mata\helpers\ComposerHelper;
use yii\base\InvalidConfigException;

abstract class Module extends BaseModule {

	public abstract function getNavigation();
	
	// TODO Cross-reference to matacms, tight coupling!
	public $layout = "@matacms/views/layouts/module"; 

	public function getVersion() {
		return $this->mataConfig->version;
	}

	public function canShowInNavigation() {
		return true;
	}

	public function getName() {
		return $this->mataConfig->name;
	}

	public function getDescription() {
		return $this->mataConfig->description;
	}

	public $mataConfig = [];

	const CONFIG_FILE_NAME = "module-config.json";
	const DEFAULT_ASSET_BUNDLE = "ModuleAsset";

	private $defaultModuleAssetBundle;

	public function __construct($id, $parent = null, $config = []) {
		$config["mataConfig"] = $this->getMataConfig();
		parent::__construct($id, $parent, $config);
	}

	public function getIcon() {

		$assetBundle = $this->getModuleAssetBundle();
		$assetBundleInstance = $assetBundle->register(\Yii::$app->view);

		return $assetBundleInstance->sourcePath . $this->mataConfig->icon;
	}

	private function getMataConfig() {
		$classInfo = new \ReflectionClass($this);
		
		$namespace = str_replace("Module", "", $classInfo->name);

		$libraryDir = ComposerHelper::getLibraryDirByNamespace($namespace);

		if ($libraryDir == null)
			throw new InvalidConfigException(sprintf("Could not find %s for namespace %s", self::CONFIG_FILE_NAME, $moduleDir));

		$configFile = $libraryDir . DIRECTORY_SEPARATOR . self::CONFIG_FILE_NAME;

		if (file_exists($configFile) == false)
			throw new InvalidConfigException(sprintf("Could not find %s", $configFile));

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