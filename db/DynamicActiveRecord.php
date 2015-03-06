<?php 

namespace mata\db;

use Yii;
use yii\db\Schema;
use yii\helpers\Inflector;

class DynamicActiveRecord extends \mata\db\ActiveRecord {

	protected static $tableName;
	private $_rules;
	private $_attributeLabels;

	public function __construct($tableName)
	{
		self::$tableName = $tableName;
		$this->setupModel($tableName);
	}

	public static function tableName() {
		return self::$tableName;
	}

	public static function setTableName($tableName)
	{
		self::$tableName = $tableName;
	}

	protected static function getDbConnection() {
        return Yii::$app->getDb();
    }

	public function rules()
	{
		return $this->_rules;
	}


	public function attributeLabels()
	{
		return $this->_attributeLabels;
	}

	private function setupModel($tableName) {
		$tableSchema = self::getDbConnection()->getTableSchema($tableName);
		if(!empty($tableSchema->columns)) {
			$fields = [];
			foreach ($tableSchema->columns as $column) {
				// if($column->name == 'Id')
				// 	continue;
				$fields[] = $column->name;
			}

			// Prepare rules
			$this->_rules = self::generateRulesFromTableSchema($tableSchema);

			// Prepare attributeLabels
			$this->_attributeLabels = self::generateLabelsFromTableSchema($tableSchema);
		}
	}

	/**
     * Generates validation rules for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated validation rules
     */
    protected static function generateRulesFromTableSchema($tableSchema)
    {
        $rules = [];
        $types = [];
        $lengths = [];
        foreach ($tableSchema->columns as $column) {
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
            $rules[] = [$columns, $type];
        }
        foreach ($lengths as $length => $columns) {
            $rules[] = [$columns, 'string', 'max' => $length];
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
                        $rules[] = [$uniqueColumns[0], 'unique'];
                    } elseif ($attributesCount > 1) {
                        $labels = array_intersect_key($this->generateLabels($tableSchema), array_flip($uniqueColumns));
                        $lastLabel = array_pop($labels);
                        $columnsList = implode("', '", $uniqueColumns);
                        $rules[] = [$columnsList, 'unique', ['targetAttribute' => [$columnsList], 'message' => 'The combination of " . implode(', ', $labels) . " and " . $lastLabel . " has already been taken.']];
                    }
                }
            }
        } catch (NotSupportedException $e) {
            // doesn't support unique indexes information...do nothing
        }
        return $rules;
    }

    /**
     * Generates the attribute labels for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated attribute labels (name => label)
     */
    protected static function generateLabelsFromTableSchema($tableSchema)
    {
        $labels = [];
        foreach ($tableSchema->columns as $column) {
            if (!strcasecmp($column->name, 'id')) {
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