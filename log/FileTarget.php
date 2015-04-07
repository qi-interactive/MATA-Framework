<?php 

namespace mata\log;

use yii\log\Logger;
/**
 *  Add the ability to remove file/line number from the logs: 
 * 
 * 2015-04-03 12:23:56 [127.0.0.1][1][-][info][cloudwatch] App initialized
 *  in /Users/wichura/Sites/icoex.co.uk/mata-cms/modules/cloudwatch/controllers/CloudwatchController.php:17
 * 
 * becomes:
 * 2015-04-03 12:23:56 [127.0.0.1][1][-][info][cloudwatch] App initialized
 * 
 * Making it perfect for simple tracking of application events.
 */  
class FileTarget extends \yii\log\FileTarget {

	public $showFileDetails = true;

	public function formatMessage($message) {
		list($text, $level, $category, $timestamp) = $message;
		$level = Logger::getLevelName($level);
		if (!is_string($text)) {
			$text = VarDumper::export($text);
		}
		$traces = [];
		if ($this->showFileDetails && isset($message[4])) {
			foreach($message[4] as $trace) {
				$traces[] = "in {$trace['file']}:{$trace['line']}";
			}
		}

		$prefix = $this->getMessagePrefix($message);
		return date('Y-m-d H:i:s', $timestamp) . " {$prefix}[$level][$category] $text"
		. (empty($traces) ? '' : "\n    " . implode("\n    ", $traces));
	}
}

?>