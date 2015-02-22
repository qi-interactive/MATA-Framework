<?php 

namespace mata\web;

use yii\web\Request as BaseRequest;

/**
 * This class allows having .htaccess in /web of a project without 
 * the need to point virtual host to /web folder
 */
class Request extends BaseRequest {

	private $_baseUrl;
	private $_scriptUrl;

	public function getScriptUrl() {
		if ($this->_scriptUrl === null) {
			$scriptFile = $this->getScriptFile();
			$scriptName = basename($scriptFile);
			if (basename($_SERVER['SCRIPT_NAME']) === $scriptName) {
				$this->_scriptUrl = $_SERVER['SCRIPT_NAME'];
			} elseif (basename($_SERVER['PHP_SELF']) === $scriptName) {
				$this->_scriptUrl = $_SERVER['PHP_SELF'];
			} elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName) {
				$this->_scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
			} elseif (($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false) {
				$this->_scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $scriptName;
			} elseif (!empty($_SERVER['DOCUMENT_ROOT']) && strpos($scriptFile, $_SERVER['DOCUMENT_ROOT']) === 0) {
				$this->_scriptUrl = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $scriptFile));
			} else {
				throw new InvalidConfigException('Unable to determine the entry script URL.');
			}
		}

		$this->_scriptUrl = str_replace("/web", "", $this->_scriptUrl);

		return $this->_scriptUrl;
	}

	public function getBaseUrl() {

		if ($this->_baseUrl === null) {
			$this->_baseUrl = rtrim(dirname($this->getScriptUrl()), '\\/');
		}

		$this->_baseUrl = str_replace("/web", "", $this->_baseUrl);

		return $this->_baseUrl;
	}
} 