<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\models;

use Yii;
use mata\behaviors\BlameableBehavior;
use mata\behaviors\IncrementalBehavior;

/**
 * This is the model class for table "{{%mata_itemorder}}".
 *
 * @property string $DocumentId
 * @property string $Grouping
 * @property integer $Order
 */
class ItemOrder extends \mata\db\ActiveRecord {
    
    public function behaviors() {
        return [
            [
                'class' => IncrementalBehavior::className(),
                'findBy' => "Grouping",
                'incrementField' => "Order"
            ]
        ];
    }

    public static function tableName()
    {
        return '{{%mata_itemorder}}';
    }

    public function rules()
    {
        return [
            [['DocumentId', 'Grouping', 'Order'], 'required'],
            [['Order'], 'integer'],
            [['Grouping'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'DocumentId' => 'Document ID',
            'Grouping' => 'Grouping',
            'Order' => 'Order',
        ];
    }
}