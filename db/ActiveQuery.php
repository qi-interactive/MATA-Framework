<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\db;

class ActiveQuery extends \yii\db\ActiveQuery {

	/**
	 * This function should be used for models that cannot be updated, such as Media.
	 * Fetching such records will use cache, if available, which does not expire
	 */ 
	public function cachedOne($db = null) {

		$modelClass = $this->modelClass;
		$command = $this->createCommand($db);

		$key = $this->modelClass . serialize($command->params);

	    $cache = Yii::$app->cache;
	    $data = $cache->get($key);

	    if ($data === false) {
	    	$data = parent::one($db);
	        $cache->set($key, $data);
	    }

	    return $data;
	}
}
