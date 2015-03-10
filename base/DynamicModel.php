<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace mata\base;

use Yii;

class DynamicModel extends \yii\base\DynamicModel {

    private $attributeLabels = [];
    public $tableName;

    /**
     * Set label for dynamic attribute
     *
     * @param integer $attribute
     * @param string $label
     */
    public function setAttributeLabel($attribute, $label)
    {
        $this->attributeLabels[$attribute] = $label;
    }
    /**
     * @inheritdoc
     */
    public function getAttributeLabels()
    {
        return $this->attributeLabels;
    }

    public function setTableName($tableName)
    {
         $this->tableName = $tableName;
    }

    public function getTableName() {
        return $this->tableName;
    }

    /**
     * Returns the text label for the specified attribute.
     * If the attribute looks like `relatedModel.attribute`, then the attribute will be received from the related model.
     * @param string $attribute the attribute name
     * @return string the attribute label
     * @see generateAttributeLabel()
     * @see attributeLabels()
     */
    public function getAttributeLabel($attribute)
    {
        $labels = $this->getAttributeLabels();
        if (isset($labels[$attribute])) {
            return ($labels[$attribute]);
        }

        return $this->generateAttributeLabel($attribute);
    }

    protected static function getDbConnection() {
        return Yii::$app->getDb();
    }

    /**
     * Returns the schema information of the DB table associated with this AR class.
     * @return TableSchema the schema information of the DB table associated with this AR class.
     * @throws InvalidConfigException if the table for the AR class does not exist.
     */
    public function getInstanceTableSchema()
    {
        $schema = self::getDbConnection()->getSchema()->getTableSchema($this->getTableName());
        if ($schema !== null) {
            return $schema;
        } else {
            throw new InvalidConfigException("The table does not exist: " . $this->getTableName());
        }
    }


}