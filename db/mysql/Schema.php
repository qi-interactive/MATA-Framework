<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\db\mysql;

use yii\db\mysql\Schema as BaseSchema;

class Schema extends BaseSchema {

	public function convertException(\Exception $e, $rawSql) {

		switch($e->getCode()) {

			case "42S02": {
				$message = $e->getMessage() .
				"\nThe SQL being executed was: $rawSql" .
				$this->getMataExceptionMessage();
				return new \yii\db\Exception($message, $e->errorInfo, (int) $e->getCode(), $e);
				break;
			}

			default: {
				return parent::convertException($e, $rawSql);
			}
		}
	}

	private function getMataExceptionMessage() {

		// The idea here is to tell the user that migrations need to be applied, and what command to run to do so
		return "";
	}
}