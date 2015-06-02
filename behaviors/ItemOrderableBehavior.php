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

      $itemOrder = new ItemOrder;
      $itemOrder->attributes = [
        "DocumentId" => $model->getDocumentId()->getId(),
        "Grouping" => get_class($model)
      ];

      if ($itemOrder->save() == false)
          throw new ServerErrorHttpException($itemOrder->getTopError());
    }

    public function afterDelete(Event $event)
    {

      $model = $event->sender;
      $class = get_class($model);

      $itemOrder = new ItemOrder;
      $deletedItemOrder = $itemOrder->find()->where(['DocumentId' => $model->getDocumentId()->getId(), 'Grouping' => $class])->one();
      if(!$deletedItemOrder->delete())
        throw new ServerErrorHttpException($deletedItemOrder->getTopError());

      $itemsOrdered = ItemOrder::find()->where(['Grouping' => $class])->orderBy('Order ASC')->all();
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
      $class = get_class($model);

    $itemOrder = new ItemOrder;
        $currentItemOrder = $itemOrder->find()->where(['DocumentId' => $model->getDocumentId()->getId(), 'Grouping' => $class])->one();
        if($currentItemOrder != null)
          $this->owner->_order = $currentItemOrder->Order;

    }

  public function setOrder($order) 
  {
    $class = get_class($this->owner);
    $itemOrder = new ItemOrder;
        $currentItemOrder = $itemOrder->find()->where(['DocumentId' => $this->owner->getDocumentId()->getId(), 'Grouping' => $class])->one();
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

  public function ordered() 
  {

    $class = $this->owner->modelClass;
    return $this->prepareQuery($class);
  }

  public function next($looped = false) 
  {
    $class = get_class($this->owner);
    $itemOrder = new ItemOrder;
    $currentItemOrder = $itemOrder->find()->where(['DocumentId' => $this->owner->getDocumentId()->getId(), 'Grouping' => $class])->one();

    if(!empty($currentItemOrder)) {
      $candidate = $this->prepareQuery($class, 'mata_itemorder.Order > :currentOrder', [':currentOrder' => $currentItemOrder->Order])->one();
      if(!empty($candidate))
        return $candidate;

      // find next article
      $candidates = $this->prepareQuery($class, 'mata_itemorder.Order > :currentOrder', [':currentOrder' => $currentItemOrder->Order])->all();
      if(!empty($candidates)) {
        foreach($candidates as $candidate) {
          if(!empty($candidate))
            return $candidate;
        }
      }

      // find next article from begining
      if($looped) {
        $candidates = $this->prepareQuery($class, 'mata_itemorder.Order > 0')->all();
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

  public function previous($looped = false) 
  {
    $class = get_class($this->owner);
    $itemOrder = new ItemOrder;
    $currentItemOrder = $itemOrder->find()->where(['DocumentId' => $this->owner->getDocumentId()->getId(), 'Grouping' => $class])->one();

    if(!empty($currentItemOrder)) {
      $candidate = $this->prepareQuery($class, 'mata_itemorder.Order < :currentOrder', [':currentOrder' => $currentItemOrder->Order], 'DESC')->one();
      if(!empty($candidate))
        return $candidate;

      // find previous article
      $candidates = $this->prepareQuery($class, 'mata_itemorder.Order < :currentOrder', [':currentOrder' => $currentItemOrder->Order], 'DESC')->all();
      if(!empty($candidates)) {
        foreach($candidates as $candidate) {
          if(!empty($candidate))
            return $candidate;
        }
      }

      // find previous article from end
      if($looped) {
        $candidates = $this->prepareQuery($class, 'mata_itemorder.Order <= (SELECT MAX(mata_itemorder.Order) FROM mata_itemorder WHERE mata_itemorder.Grouping = :grouping)', [':grouping' => $class], 'DESC')->all();
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

  public function first() 
  {
    $class = get_class($this->owner);

    return $this->prepareQuery($class, 'mata_itemorder.Order > 0')->one();
  }

  public function last() 
  {
    $class = get_class($this->owner);

    return $this->prepareQuery($class, 'mata_itemorder.Order = (SELECT MAX(mata_itemorder.Order) FROM mata_itemorder WHERE mata_itemorder.Grouping = :grouping)', [':grouping' => $class], 'DESC')->one(); 
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

  protected function prepareQuery($class, $whereCondition = false, $whereParams = [], $sort = 'ASC') 
  {
    $aliasWithPk = $this->getAliasWithPk($class);
    $model = new $class;
        $query = $model->find();

        $query->join('INNER JOIN', 'mata_itemorder', 'mata_itemorder.DocumentId = CONCAT(:class, '.$aliasWithPk.') AND mata_itemorder.Grouping = :grouping', [':class' => $class . '-', ':grouping' => $class]);
        if(!empty($whereCondition))
          $query->andWhere($whereCondition, $whereParams);
        $query->orderBy('mata_itemorder.Order ' . $sort);
        return $query;    
  }

}
