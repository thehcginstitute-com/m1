<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Notfound
 */
class Amasty_Notfound_Model_Mysql4_Attempt_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    function _construct()
    {
        $this->_init('amnotfound/attempt');
    }
    
    function addStartDateFilter($date)
    {
        $this->addFieldToFilter('date', array('gt'=>$date));
    }  
    
    function addIpFilter($ip)
    {
        $this->addFieldToFilter('client_ip', $ip);
    }      
}