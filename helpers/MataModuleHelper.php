<?php

namespace mata\helpers;

class MataModuleHelper {

	public static function isMataModule($module) {
		return is_a($module, "mata\base\Module");
	}
}
