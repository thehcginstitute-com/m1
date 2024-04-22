<?php
# 2024-04-22 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `Ebizmarts_MailChimp_Model_Synchbatches`": https://github.com/thehcginstitute-com/m1/issues/575
final class Ebizmarts_MailChimp_Model_Synchbatches extends Mage_Core_Model_Abstract {
	/**
	 * 2024-04-22 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * 1) "Refactor `Ebizmarts_MailChimp_Model_Synchbatches`": https://github.com/thehcginstitute-com/m1/issues/575
	 * 2) A string like «ssolgd3wxj».
	 */
	function id():string {return $this['batch_id'];}

    /**
     * 2024-04-22 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Synchbatches`": https://github.com/thehcginstitute-com/m1/issues/575
	 * @override
	 * @see Varien_Object::_construct()
	 * @used-by Varien_Object::__construct()
     */
    protected function _construct():void {$this->_init('mailchimp/synchbatches');}
}