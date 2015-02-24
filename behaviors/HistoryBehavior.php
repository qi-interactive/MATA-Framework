<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace mata\behaviors;

use Yii;
use yii\base\Event;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class HistoryBehavior extends Behavior {

    public function events() {
       return [
            BaseActiveRecord::EVENT_AFTER_FIND => "afterFind",
            BaseActiveRecord::EVENT_AFTER_INSERT => "afterSave",
            BaseActiveRecord::EVENT_AFTER_UPDATE => "afterSave",
            BaseActiveRecord::EVENT_AFTER_DELETE => "afterDelete"
        ];
    }

    public function afterFind(Event $event) {
        $revision = $this->getLatestRevision($event->sender);

        if ($revision != null)
            $event->sender->attributes = $revision->attributes;

    }

    public function afterSave(Event $event) {
        
    }

    public function afterDelete(Event $event) {
        
    }

    private function getLatestRevision(BaseActiveRecord $model) {
        $documentId = $this->getDocumentId($model);
        return DocumentHistory::findOne($documentId);
    }

    private function getDocumentId(BaseActiveRecord $model) {

        $pk = $model->primaryKey;

        if (is_array($pk))
            $pk = implode('-', $pk);

        return get_class($model) . $pk;
    }
}
