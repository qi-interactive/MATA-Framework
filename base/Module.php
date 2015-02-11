<?php 

namespace mata\base;
use yii\base\Module as BaseModule;

abstract class Module extends BaseModule {

	public abstract function getVersion();
	public abstract function getName();

}