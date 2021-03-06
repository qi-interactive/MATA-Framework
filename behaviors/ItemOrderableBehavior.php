<?php

/**
* @link http://www.matacms.com/
* @copyright Copyright (c) 2015 Qi Interactive Limited
* @license http://www.matacms.com/license/
*/

namespace mata\behaviors;

use yii\base\Model;
use yii\base\Event;
use yii\db\BaseActiveRecord;
use yii\web\ServerErrorHttpException;
use mata\models\ItemOrder;
use mata\interfaces\ItemOrderableInterface;

class ItemOrderableBehavior extends \yii\base\Behavior implements ItemOrderableInterface
{

    public $_order;
    public $groupingField = null;


    public function events()
    {

        $events = [
            BaseActiveRecord::EVENT_AFTER_INSERT => "afterSave",
            BaseActiveRecord::EVENT_AFTER_DELETE => "afterDelete",
            BaseActiveRecord::EVENT_AFTER_FIND => "afterFind"
        ];

        return $events;
    }

    public function afterSave(Event $event)
    {

        $model = $event->sender;

        $grouping = $this->getGrouping($model);

        $itemOrder = new ItemOrder;
        $itemOrder->attributes = [
            "DocumentId" => $model->getDocumentId()->getId(),
            "Grouping" => $grouping
        ];

        if ($itemOrder->save() == false)
        throw new ServerErrorHttpException($itemOrder->getTopError());
    }

    public function afterDelete(Event $event)
    {

        $model = $event->sender;
        $grouping = $this->getGrouping($model);

        $itemOrder = new ItemOrder;
        $deletedItemOrder = $itemOrder->find()->where(['DocumentId' => $model->getDocumentId()->getId(), 'Grouping' => $grouping])->one();
        if(!$deletedItemOrder->delete())
        throw new ServerErrorHttpException($deletedItemOrder->getTopError());

        $itemsOrdered = ItemOrder::find()->where(['Grouping' => $grouping])->orderBy('Order ASC')->all();
        if(!empty($itemsOrdered)) {
            foreach($itemsOrdered as $index => $itemOrder) {
                $itemOrder->Order = $index+1;
                if(!$itemOrder->save(false))
                throw new ServerErrorHttpException($itemOrder->getTopError());
            }
        }
    }

    public function afterFind(Event $event)
    {

        $model = $event->sender;
        $grouping = $this->getGrouping($model);

        $currentItemOrder = ItemOrder::find()->where(['DocumentId' => $model->getDocumentId()->getId(), 'Grouping' => $grouping])->one();
        if($currentItemOrder != null)
            $this->owner->_order = $currentItemOrder->Order;

    }

    public function applyOrder($order)
    {

        $grouping = $this->getGrouping($this->owner);

        $itemOrder = new ItemOrder;
        $currentItemOrder = $itemOrder->find()->where(['DocumentId' => $this->owner->getDocumentId()->getId(), 'Grouping' => $grouping])->one();
        if(!$currentItemOrder)
        return;

        $currentItemOrder->Order = $order;
        if ($currentItemOrder->save(false) == false) {
            throw new ServerErrorHttpException($currentItemOrder->getTopError());
        }
    }

    public function getOrder()
    {
        return $this->owner->_order;
    }

    public function ordered($grouping = null)
    {
        return $this->prepareQuery($this->owner, $grouping);
    }

    public function next($looped = false, $whereCondition = false, $whereParams = [])
    {

        $grouping = $this->getGrouping($this->owner);

        $itemOrder = new ItemOrder;
        $currentItemOrder = $itemOrder->find()->where(['DocumentId' => $this->owner->getDocumentId()->getId(), 'Grouping' => $grouping])->one();

        $queryWhereCondition = 'mata_itemorder.Order > :currentOrder';
        $queryWhereParams = [':currentOrder' => $currentItemOrder->Order];

        if($whereCondition != false) {
            $queryWhereCondition .= ' AND ' . $whereCondition;
            $queryWhereParams = \yii\helpers\ArrayHelper::merge($queryWhereParams, $whereParams);
        }

        if(!empty($currentItemOrder)) {
            $candidate = $this->prepareQuery($this->owner, $grouping, $queryWhereCondition, $queryWhereParams)->one();
            if(!empty($candidate))
            return $candidate;

            // find next article
            $candidates = $this->prepareQuery($this->owner, $grouping, $queryWhereCondition, $queryWhereParams)->all();
            if(!empty($candidates)) {
                foreach($candidates as $candidate) {
                    if(!empty($candidate))
                    return $candidate;
                }
            }

            // find next article from begining
            if($looped) {
                $queryWhereCondition = 'mata_itemorder.Order > 0';
                $queryWhereParams = [];

                if($whereCondition != false) {
                    $queryWhereCondition .= ' AND ' . $whereCondition;
                    $queryWhereParams = \yii\helpers\ArrayHelper::merge($queryWhereParams, $whereParams);
                }
                $candidates = $this->prepareQuery($this->owner, $grouping, $queryWhereCondition, $queryWhereParams)->all();
                if(!empty($candidates)) {
                    foreach($candidates as $candidate) {
                        if(!empty($candidate))
                        return $candidate;
                    }
                }
            }
        }

        return null;
    }

    public function previous($looped = false, $whereCondition = false, $whereParams = [])
    {

        $grouping = $this->getGrouping($this->owner);

        $itemOrder = new ItemOrder;
        $currentItemOrder = $itemOrder->find()->where(['DocumentId' => $this->owner->getDocumentId()->getId(), 'Grouping' => $grouping])->one();

        $queryWhereCondition = 'mata_itemorder.Order < :currentOrder';
        $queryWhereParams = [':currentOrder' => $currentItemOrder->Order];

        if($whereCondition != false) {
            $queryWhereCondition .= ' AND ' . $whereCondition;
            $queryWhereParams = \yii\helpers\ArrayHelper::merge($queryWhereParams, $whereParams);
        }

        if(!empty($currentItemOrder)) {
            $candidate = $this->prepareQuery($this->owner, $grouping, $queryWhereCondition, $queryWhereParams, 'DESC')->one();
            if(!empty($candidate))
            return $candidate;

            // find previous article
            $candidates = $this->prepareQuery($this->owner, $grouping, $queryWhereCondition, $queryWhereParams, 'DESC')->all();
            if(!empty($candidates)) {
                foreach($candidates as $candidate) {
                    if(!empty($candidate))
                    return $candidate;
                }
            }

            // find previous article from end
            if($looped) {
                $queryWhereCondition = 'mata_itemorder.Order <= (SELECT MAX(mata_itemorder.Order) FROM mata_itemorder WHERE mata_itemorder.Grouping = :grouping)';
                $queryWhereParams = [':grouping' => $grouping];

                if($whereCondition != false) {
                    $queryWhereCondition .= ' AND ' . $whereCondition;
                    $queryWhereParams = \yii\helpers\ArrayHelper::merge($queryWhereParams, $whereParams);
                }
                $candidates = $this->prepareQuery($this->owner, $grouping, $queryWhereCondition, $queryWhereParams, 'DESC')->all();

                if(!empty($candidates)) {
                    foreach($candidates as $candidate) {
                        if(!empty($candidate))
                        return $candidate;
                    }
                }
            }
        }

        return null;
    }

    public function first($whereCondition = false, $whereParams = [])
    {
        $grouping = $this->getGrouping($this->owner);

        $queryWhereCondition = 'mata_itemorder.Order > 0';
        $queryWhereParams = [];

        if($whereCondition != false) {
            $queryWhereCondition .= ' AND ' . $whereCondition;
            $queryWhereParams = \yii\helpers\ArrayHelper::merge($queryWhereParams, $whereParams);
        }

        return $this->prepareQuery($this->owner, $grouping, $queryWhereCondition, $queryWhereParams)->one();
    }

    public function last()
    {
        $grouping = $this->getGrouping($this->owner);

        $queryWhereCondition = 'mata_itemorder.Order = (SELECT MAX(mata_itemorder.Order) FROM mata_itemorder WHERE mata_itemorder.Grouping = :grouping)';
        $queryWhereParams = [':grouping' => $grouping];

        if($whereCondition != false) {
            $queryWhereCondition .= ' AND ' . $whereCondition;
            $queryWhereParams = \yii\helpers\ArrayHelper::merge($queryWhereParams, $whereParams);
        }

        return $this->prepareQuery($this->owner, $grouping, $queryWhereCondition, $queryWhereParams, 'DESC')->one();
    }

    protected function getAliasWithPk($class)
    {
        $alias = $class::getTableSchema()->name;
        $pk = $class::getTableSchema()->primaryKey;

        if (is_array($pk)) {
            if(count($pk) > 1)
            throw new NotFoundHttpException('Combined primary keys are not supported.');
            $pk = $pk[0];
        }

        return $alias . '.' . $pk;
    }

    protected function prepareQuery($owner, $grouping = null, $whereCondition = false, $whereParams = [], $sort = 'ASC')
    {
        $hasModelClass = isset($owner->modelClass);
        $class = isset($owner->modelClass) ? $owner->modelClass : get_class($owner);

        $aliasWithPk = $this->getAliasWithPk($class);
        if($grouping==null)
        $grouping = $class;


        if(!$owner instanceof \yii\db\ActiveQuery) {
            $query = $class::find();
            $query->join('INNER JOIN', 'mata_itemorder', 'mata_itemorder.DocumentId = CONCAT(:class, '.$aliasWithPk.') AND mata_itemorder.Grouping = :grouping', [':class' => $class . '-', ':grouping' => $grouping]);
            if(!empty($whereCondition))
            $query->andWhere($whereCondition, $whereParams);
            $query->orderBy('mata_itemorder.Order ' . $sort);
            return $query;
        }

        $owner->join('INNER JOIN', 'mata_itemorder', 'mata_itemorder.DocumentId = CONCAT(:class, '.$aliasWithPk.') AND mata_itemorder.Grouping = :grouping', [':class' => $class . '-', ':grouping' => $grouping]);
        if(!empty($whereCondition))
        $owner->andWhere($whereCondition, $whereParams);
        $owner->orderBy('mata_itemorder.Order ' . $sort);

        return $owner;
    }

    protected function getGrouping($model)
    {
        $grouping = get_class($model);
        $field = $this->groupingField;

        if($field != null) {
            $grouping = $field !== 'DocumentId' ? get_class($model) . '::' . $field . '::' . $model->$field : $model->$field->getId();
        }

        return $grouping;
    }

}
