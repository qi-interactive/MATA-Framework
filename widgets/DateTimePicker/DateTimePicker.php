<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\widgets\DateTimePicker;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

/**
 * Class DateTimePicker
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class DateTimePicker extends InputWidget
{
    /**
     * @var array the HTML attributes for the input tag.
     */
    public $options = [];

    /**
     * @var array options for datetimepicker
     */
    public $clientOptions = [];

    /**
     * @var array events for datetimepicker
     */
    public $clientEvents = [];

    public function init()
    {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->getId();
        }
    }

    public function run()
    {
        echo Html::beginTag('div', ['class' => 'datetimepicker-field-wrapper']);
        if ($this->hasModel()) {
            echo Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textInput($this->name, $this->value, $this->options);
        }
        echo Html::endTag('div');
        $view = $this->getView();
        DateTimePickerAsset::register($view);
        $id = $this->options['id'];
        $options = empty($this->clientOptions) ? '' : Json::encode($this->clientOptions);
        $js = "jQuery('#$id').datetimepicker($options)";
        $view->registerJs($js . ';');
    }
}
