<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\base;

class DocumentId {

	private $id;

	public function __construct($documentId = null) {
		$this->id = $documentId;
	}

	public function getPk() {
		return $this->getPkInternal();
	}

	public function getId() {
		return $this->id;
	}

	/**
	 * Useful for getting keys for properties that should apply to whole model families,
	 * not only specific models. Compare: 
	 * matacms\contentblock\models\ContentBlock-2::Title::show-field vs 
	 * matacms\contentblock\models\ContentBlock::Title::show-field
	 **/
	public function getIdNoPk() {
		$components = $this->getComponents();

		$retVal = $components[1];

		if (isset($components[3]))
			$retVal .= "::" . $components[3];

		if (isset($components[4]))
			$retVal .= "::" . $components[4];

		return $retVal;
	}

	private function getPkInternal() {
		
		$components = $this->getComponents();

		if (isset($components[2]))
			return $components[2];

	}

	public function getModel() {
		$components = $this->getComponents();

		if (empty($components) || count($components) < 2)
			return null;

		$namespaceWithClass = $components[1];
		return $namespaceWithClass::findOne($components[2]);

	}

	/**
	 * Returns: 
	 * [0] while string
	 * [1] namespaceWithClass
	 * [2] [pk]
	 * [3] attribute
	 * [4] property
	 * Can return NULL.
	 * */
	private function getComponents() {

		if ($this->id == null)
			return null;

		// check for e.g. matacms\contentblock\models\ContentBlock-1::Title::show-field
		preg_match("/([a-zA-Z\\\\]*)-(\d*)\:\:([a-zA-Z]*)\:\:([a-zA-Z-]*)/", $this->id, $output);

		if (count($output) > 0)
			return $output ?: [];

		// check for e.g. matacms\contentblock\models\ContentBlock-1::Title
		preg_match("/([a-zA-Z\\\\]*)-(\d*)\:\:([a-zA-Z]*)/", $this->id, $output);

		if (count($output) > 0)
			return $output ?: [];

		// check for e.g. matacms\contentblock\models\ContentBlock-1
		preg_match("/([a-zA-Z\\\\]*)-(\d*)/", $this->id, $output);

		if (count($output) > 0)
			return $output ?: [];

		// check for e.g. matacms\contentblock\models\ContentBlock
		preg_match("/([a-zA-Z\\\\]*)/", $this->id, $output);

		if (count($output) > 0)
			return $output ?: [];
	}

	public function __toString() {
		return $this->id ?: "";
	}
}
