<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Notfound
 */  
class Amasty_Notfound_Model_Log extends Amasty_Notfound_Model_Abstract
{
    protected $modelName = 'log';

    public function hasRedirect()
    {
        return $this->getResource()->hasRedirect($this->getRequestPath(), $this->getStoreId());
    }

}