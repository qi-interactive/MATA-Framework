<?php

namespace mata\helpers;

use Yii;
use mata\db\DynamicActiveRecord;
use yii\db\Schema;

class MataDynamicModelHelper {

	public static function generateFromTableName($tableName, $omitId = true) {
		if(!empty($tableName)) {
    		return new DynamicActiveRecord($tableName);
        }
        return null;
	}

	protected static function getDbConnection() {
        return Yii::$app->getDb();
    }

    /**
     * Generates validation rules for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated validation rules
     */
    protected static function generateRulesFromTable($dynamicModel, $tableSchema, $omitId = true)
    {
        $types = [];
        $lengths = [];
        foreach ($tableSchema->columns as $column) {
            if($column->name == 'Id' && $omitId)
                continue;
            if ($column->autoIncrement) {
                continue;
            }
            if (!$column->allowNull) {
                $types['required'][] = $column->name;
            }
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                    $types['integer'][] = $column->name;
                    break;
                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $types['safe'][] = $column->name;
                    $types['date'][] = $column->name;
                    break;
                default: // strings
                    if ($column->size > 0) {
                        $lengths[$column->size][] = $column->name;
                    } else {
                        $types['string'][] = $column->name;
                    }
                    if (stripos($column->name, 'email') !== false) {
			            $types['email'][] = $column->name;
			        } elseif (stripos($column->name, 'url') !== false) {
			            $types['url'][] = $column->name;
			        }
            }
        }
        // $rules = [];
        foreach ($types as $type => $columns) {
            $dynamicModel->addRule($columns, $type);
        }
        foreach ($lengths as $length => $columns) {
            $dynamicModel->addRule($columns, 'string', ['max' => $length]);
        }

        // Unique indexes rules
        try {
            $db = self::getDbConnection();
            $uniqueIndexes = $db->getSchema()->findUniqueIndexes($tableSchema);
            foreach ($uniqueIndexes as $uniqueColumns) {
                // Avoid validating auto incremental columns
                if (!$this->isColumnAutoIncremental($tableSchema, $uniqueColumns)) {
                    $attributesCount = count($uniqueColumns);

                    if ($attributesCount == 1) {
                        $dynamicModel->addRule($uniqueColumns[0], 'unique');
                    } elseif ($attributesCount > 1) {
                        $labels = array_intersect_key($this->generateLabels($tableSchema), array_flip($uniqueColumns));
                        $lastLabel = array_pop($labels);
                        $columnsList = implode("', '", $uniqueColumns);
                        $dynamicModel->addRule($columnsList, 'unique', ['targetAttribute' => [$columnsList], 'message' => 'The combination of " . implode(', ', $labels) . " and " . $lastLabel . " has already been taken.']);
                    }
                }
            }
        } catch (NotSupportedException $e) {
            // doesn't support unique indexes information...do nothing
        }
        return $dynamicModel;
    }

    /**
     * Generates the attribute labels for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated attribute labels (name => label)
     */
    protected static function generateLabels($tableSchema)
    {
        $labels = [];
        foreach ($tableSchema->columns as $column) {
            if ($this->generateLabelsFromComments && !empty($column->comment)) {
                $labels[$column->name] = $column->comment;
            } elseif (!strcasecmp($column->name, 'id')) {
                $labels[$column->name] = 'ID';
            } else {
                $label = Inflector::camel2words($column->name);
                if (!empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
                    $label = substr($label, 0, -3) . ' ID';
                }
                $labels[$column->name] = $label;
            }
        }

        return $labels;
    }

}