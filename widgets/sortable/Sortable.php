<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-sortable
 * @version 1.2.0
 */

namespace mata\widgets\sortable;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * Create sortable lists and grids using HTML5 drag and drop API for Yii 2.0.
 * Based on html5sortable plugin.
 *
 * @see http://farhadi.ir/projects/html5sortable/
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class Sortable extends \kartik\sortable\Sortable
{

    /**
     * Register client assets
     */
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