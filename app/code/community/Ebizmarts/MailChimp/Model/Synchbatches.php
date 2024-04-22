<?php
class Ebizmarts_MailChimp_Model_Synchbatches extends Mage_Core_Model_Abstract
{
    /**
     * Initialize model
     *
     * @return void
     */
    function _construct()
    {
        parent::_construct();
        $this->_init('mailchimp/synchbatches');
    }
}
