<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\behaviors;

use Yii;
use yii\base\Event;
use yii\db\BaseActiveRecord;

/**
 * 
 * Strong link behavior should be used when deleting a record depends on a 
 * (non)existence of another. For instance, if a project is assigned to a client, and 
 * [[Client]] has a [[StrongLinkBehavior]] to [[WorkProject]], which would look like this: 
 * 
 *           [
 *               'class' =>  StrongLinkBehavior::className(),
 *               'links' => 'workProjects'
 *           ]
 *
 * then the behavior will prevent deletion of this record, unless there are 
 * no [[WorkProject]] assigned to that client.
 *  
 */ 
class StrongLinkBehavior extends \yii\base\Behavior {

	public $links = [];

	public function init() {
		if (is_string($this->links))
			$this->links = [$this->links];
	}

	public function events() {
		return [
		BaseActiveRecord::EVENT_BEFORE_DELETE => "onBeforeDelete"
		];
	}

	public function onBeforeDelete(Event $event) {

		$model = $this->owner;

		foreach ($this->links as $link) {

			$linkedModels = is_callable($link) ? $link($model) : $model->$link;

			if (!empty($linkedModels)) {

				$linkedLabels = [];

				$linkedCount = count($linkedModels);

				foreach ($linkedModels as $linkedObj)
					$linkedLabels[] = $linkedObj->getLabel();

				$firstLink = $linkedModels[0];

				$model->addError("Name", sprintf("Cannot delete <strong>%s</strong> as it has a %d linked %s:<strong> %s </strong>", $model->getLabel(), $linkedCount, $firstLink->getModelLabel(), implode($linkedLabels, ", ")));

				$event->isValid = false;

				break;
			}
		}
	}
}
