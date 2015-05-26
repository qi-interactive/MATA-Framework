<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

use yii\db\Schema;
use mata\user\migrations\Migration;

class m150523_161228_init extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%mata_itemorder}}', [
            'DocumentId'   => Schema::TYPE_STRING . '(128) NOT NULL',
            'Grouping' => Schema::TYPE_STRING . '(128) NOT NULL',
            'Order' =>  Schema::TYPE_INTEGER . ' NOT NULL'
            ]);

        $this->addPrimaryKey("PK_DocumentGroup", "{{%mata_itemorder}}", ["DocumentId", "Grouping"]);
    }

    public function safeDown() {
        $this->dropTable('{{%mata_itemorder}}');
    }
}
