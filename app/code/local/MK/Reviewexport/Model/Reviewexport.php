<?php

class MK_Reviewexport_Model_Review extends Mage_Core_Model_Abstract
{
    function _construct()
    {
        parent::_construct();
        $this->_init('reviewexport/reviewexport');
    }
}