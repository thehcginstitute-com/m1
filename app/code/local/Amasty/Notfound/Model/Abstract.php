<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Notfound
 */  
class Amasty_Notfound_Model_Abstract extends Mage_Core_Model_Abstract
{
    protected $modelName = 'log';
    
    function _construct()
    {    
        $this->_init('amnotfound/' . $this->modelName);
    }
    
    function clear()
    {
        return $this->getResource()->clear();
    }
    
    function getCountFrom($lastRun=0)
    {
        $this->collect($lastRun);
        $collection = $this->getCollection()
            ->addFieldToFilter('date', array('gt' => date('Y-m-d H:i:s', $lastRun)));
        return $collection->count();        
    }
    
    function collect($lastRun=0)
    {
        return $this->getResource()->collect($lastRun);
    }    
}