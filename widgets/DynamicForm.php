<?php

namespace mata\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use ReflectionClass;
use yii\helpers\ArrayHelper;

/*
 * Usage:
 * 
 * use mata\widgets\DynamicForm;
 * use mata\helpers\MataDynamicModelHelper; (to create DynamicModel from tableName)
 *
 * echo DynamicForm::widget([
        'model' => new \mata\form\models\Form,
        // 'model' => MataDynamicModelHelper::generateFromTableName('form_contact'),
        'fieldAttributes' => [
            'Name' => [
                'label' => "Your Name",
                'fieldType' => "wysiwyg"
            ],
            'ReferencedTable' => [
                'label' => "Referenced Table Name"
            ],
            // examples:
            // 
            // 'ReferencedTable' => [
            //  'label' => "Email Address",
            //  'fieldType' => [
            //      'textarea' => [
            //          'params' => ["rows" => 5]
            //      ]
            //  ]
            // ],
            // 'ReferencedTable' => [
            //  'label' => "Email Address",
            //  'fieldType' => [
            //      'dropDownList' => [
            //          'params' => ["items" => [1,2,3]]
            //      ]
            //  ]
            // ]
        ]
    ]);

    If a field is to be ommited, pass false as the value: 

    echo DynamicForm::widget([
        'model' => $model,
        'fieldAttributes' => [
            'Grouping' => false
        ]
        ]);
 *
 *
 *
 * 
 */

class DynamicForm extends \matacms\widgets\ActiveForm {

    public $model;
    public $action = '';
    public $fieldAttributes = [];
    public $omitId = true;
    public $autoRenderFields = true;
    private $modelAttributes;

	/**
     * Initializes the widget.
     * This renders the form open tag.
     */
	public function init()
	{
		if (!isset($this->options['id'])) {
			$this->options['id'] = $this->getId();
		}

        $this->options = ArrayHelper::merge([
            "submitButtonText" => "Submit"
            ], $this->options);

        echo Html::beginForm($this->action, $this->method, $this->options);
    }

    /**
     * Runs the widget.
     * This registers the necessary javascript code and renders the form close tag.
     * @throws InvalidCallException if `beginField()` and `endField()` calls are not matching
     */
    public function run()
    {
    	if (!empty($this->_fields)) {
    		throw new InvalidCallException('Each beginField() should have a matching endField() call.');
    	}

        // Set custom attribute labels
        if(!empty($this->fieldAttributes)) {
            $attributeLabels = $this->model->attributeLabels();
            foreach($this->fieldAttributes as $fieldName => $fieldAttribute) {
                if(array_key_exists($fieldName, $attributeLabels) && isset($fieldAttribute['label'])) {
                    $this->model->setAttributeLabel($fieldName, $fieldAttribute['label']);
                }
            }
        }

        $modelClass = (new ReflectionClass($this->model))->getName();

        $this->modelAttributes = $this->model->attributes;
        // Remove Id from model attributes
        if($this->omitId) {
            if(array_key_exists('Id', $this->modelAttributes)) {
                unset($this->modelAttributes['Id']);
            }
        }

        // Generate fields
        if($this->autoRenderFields) {
            foreach($this->modelAttributes as $fieldName => $fieldValue)
                echo $this->generateActiveField($fieldName);
        }        
        

        if ($this->enableClientScript) {
            $id = $this->options['id'];
            $options = Json::encode($this->getClientOptions());
            $attributes = Json::encode($this->attributes);
            $view = $this->getView();
            \yii\widgets\ActiveFormAsset::register($view);
            $view->registerJs("jQuery('#$id').yiiActiveForm($attributes, $options);");
        }

        if($this->autoRenderFields)
            echo $this->submitBtns();

        echo Html::endForm();
    }

    protected function submitBtns() {
    	echo Html::beginTag('div', ['class'=>'form-group']);
    	echo Html::submitButton($this->options['submitButtonText'], ['class' => 'btn btn-success']);
    	echo Html::endTag('div');
    }

    /**
     * Generates code for active field
     * @param string $attribute
     * @return string
     */
    public function generateActiveField($attribute, $options = []) {

        if (isset($this->fieldAttributes[$attribute]) && $this->fieldAttributes[$attribute] == false)
            return;

        if(array_key_exists($attribute, $this->fieldAttributes) && isset($this->fieldAttributes[$attribute]['fieldType'])) {
            $fieldType = $this->fieldAttributes[$attribute]['fieldType'];
            $basicField = $this->field($this->model, $attribute, $options);
            $fieldTypeParams = [];
            if(is_array($fieldType)) {
                if(isset($this->fieldAttributes[$attribute]['fieldType'][key($fieldType)]['params'])) {
                    $fieldTypeParams[] = $this->fieldAttributes[$attribute]['fieldType'][key($fieldType)]['params'];
                }
                $fieldType = key($fieldType);
            }
            return call_user_func_array(array($basicField, $fieldType), $fieldTypeParams);
        }

        $tableSchema = $this->model->getTableSchema();
        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return $this->field($this->model, $attribute, $options)->passwordInput();
            } else {
                return $this->field($this->model, $attribute, $options);
            }
        }
        $column = $tableSchema->columns[$attribute];
        if ($column->phpType === 'boolean') {
            return $this->field($this->model, $attribute, $options)->checkbox();
        } elseif ($column->type === 'text') {
            return $this->field($this->model, $attribute, $options)->textarea(['rows' => 6]);
        } else {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
                $input = 'passwordInput';
            } else {
                $input = 'textInput';
            }
            if (is_array($column->enumValues) && count($column->enumValues) > 0) {
                $dropDownOptions = [];
                foreach ($column->enumValues as $enumValue) {
                    $dropDownOptions[$enumValue] = Inflector::humanize($enumValue);
                }
                return $this->field($this->model, $attribute, $options)->dropDownList(preg_replace("/\n\s*/", ' ', VarDumper::export($dropDownOptions)), ['prompt' => '']);
            } elseif ($column->phpType !== 'string' || $column->size === null) {
                return $this->field($this->model, $attribute, $options)->$input();
            } else {
                return $this->field($this->model, $attribute, $options)->$input(['maxlength' => $column->size]);
            }
        }
    }
    
}