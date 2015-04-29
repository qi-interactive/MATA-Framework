<?php

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
