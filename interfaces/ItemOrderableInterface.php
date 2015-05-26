<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace mata\interfaces;

interface ItemOrderableInterface
{

	public function setOrder($order);

    public function ordered();

    public function next();

    public function previous();
    
    public function first();

    public function last();
}
