<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\widgets\sortable;

class SortableAsset extends \kartik\base\AssetBundle
{

    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('css', ['css/kv-sortable']);
        $this->setupAssets('js', ['js/kv-widgets', 'js/html.sortable', 'js/mata-sortable']);
        parent::init();
    }
}
