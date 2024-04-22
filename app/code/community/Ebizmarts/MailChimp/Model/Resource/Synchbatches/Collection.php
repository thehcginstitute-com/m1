<?php
class Ebizmarts_MailChimp_Model_Resource_Synchbatches_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

	/**
	 * Set resource type
	 *
	 * @return void
	 */
	function _construct()
	{
		parent::_construct();
		$this->_init('mailchimp/synchbatches');
	}
}
