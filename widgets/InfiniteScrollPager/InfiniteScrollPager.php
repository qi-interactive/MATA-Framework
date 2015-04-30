<?php

namespace mata\widgets\InfiniteScrollPager;

use yii\helpers\Json;
use yii\widgets\Pjax;
use yii\helpers\Html;

class InfiniteScrollPager extends \yii\widgets\LinkPager
{

    public $clientOptions;
    
    public function init()
    {
        parent::init();
        $this->options['class'] = 'pagination hidden';
    }

    public function run() {

        $clientOptions = [
            'pjax' => [
                'id' => $this->clientOptions['pjax']['id']
            ],
            'id' => $this->clientOptions['listViewId']
        ];

        $clientOptions = Json::encode($this->clientOptions);

        $view = $this->getView();
        InfiniteScrollPagerAsset::register($view);
        $view->registerJs("mata.infinitePager.init($clientOptions);");

        parent::run();        
    }

    protected function renderPageButtons() {
        $retVal = parent::renderPageButtons();
        $retVal .= Html::tag("div", "", ["class" => "loader"]);
        return $retVal;
        
    }


}