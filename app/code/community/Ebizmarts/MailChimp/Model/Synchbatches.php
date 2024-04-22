<?php
# 2024-04-22 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `Ebizmarts_MailChimp_Model_Synchbatches`": https://github.com/thehcginstitute-com/m1/issues/575
class Ebizmarts_MailChimp_Model_Synchbatches extends Mage_Core_Model_Abstract {
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