<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\widgets\InfiniteScrollPager;

use yii\web\AssetBundle;

class InfiniteScrollPagerAsset extends AssetBundle
{
	public $sourcePath = '@vendor/mata/mata-framework/widgets/InfiniteScrollPager/assets';

	public $css = [
		'css/infinitepager.css'
	];
    
	public $js = [
		'js/infinitepager.js'
	];
}
