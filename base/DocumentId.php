<?php

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
	 * 
	 * Can return NULL.
	 * */
	private function getComponents() {

		if ($this->id == null)
			return null;

		preg_match("/([a-zA-Z\\\\]*)-(\d*)/", $this->id, $output);
		return $output ?: [];
	}

	public function __toString() {
		return $this->id ?: "";
	}

}


class DocumentIdComponents {

}