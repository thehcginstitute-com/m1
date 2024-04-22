<?php
# 2024-04-22 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `Ebizmarts_MailChimp_Model_Synchbatches`": https://github.com/thehcginstitute-com/m1/issues/575
final class Ebizmarts_MailChimp_Model_Synchbatches extends Mage_Core_Model_Abstract {
	/**
	 * 2024-04-22 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * 1) "Refactor `Ebizmarts_MailChimp_Model_Synchbatches`": https://github.com/thehcginstitute-com/m1/issues/575
	 * 2) A string like «ssolgd3wxj».
	 * @used-by HCG\MailChimp\Batch\GetResults::_saveItemStatus()
	 * @used-by HCG\MailChimp\Batch\GetResults::p()
	 */
	function id():string {return $this['batch_id'];}

	/**
	 * 2024-04-22 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * 1) "Refactor `Ebizmarts_MailChimp_Model_Synchbatches`": https://github.com/thehcginstitute-com/m1/issues/575
	 * 2) https://3v4l.org/0jL8q
	 * @used-by HCG\MailChimp\Batch\GetResults::_saveItemStatus()
	 */
	function setStatus(string $v):self {$this['status'] = $v; return $this;}

    /**
     * 2024-04-22 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Synchbatches`": https://github.com/thehcginstitute-com/m1/issues/575
	 * @override
	 * @see Varien_Object::_construct()
	 * @used-by Varien_Object::__construct()
     */
    protected function _construct():void {$this->_init('mailchimp/synchbatches');}
}