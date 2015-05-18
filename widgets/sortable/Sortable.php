<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\widgets\sortable;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class Sortable extends \kartik\sortable\Sortable
{

    public function registerAssets()
    {
        $view = $this->getView();
        \mata\widgets\sortable\SortableAsset::register($view);
        $this->registerPlugin('matasortable');
        $id = 'jQuery("#' . $this->options['id'] . '")';
        if ($this->disabled) {
            $js = "{$id}.matasortable('disable');";
        } else {
            $js = "{$id}.matasortable('enable');";
        }
        $view->registerJs($js);
    }
}
