<?php
# 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
class Ebizmarts_MailChimp_Model_Cron
{
	/**
	 * @var Ebizmarts_MailChimp_Helper_Data
	 */
	protected $_mailChimpHelper;
	/**
	 * @var Ebizmarts_MailChimp_Helper_Migration
	 */
	protected $_mailChimpMigrationHelper;

	function __construct()
	{
		$this->_mailChimpHelper = hcg_mc_h();
		$this->_mailChimpMigrationHelper = Mage::helper('mailchimp/migration');
	}

	function syncEcommerceBatchData()
	{
		if ($this->getMigrationHelper()->migrationFinished()) {
			hcg_mc_batches_new()->handleEcommerceBatches();
		} else {
			$this->getMigrationHelper()->handleMigrationUpdates();
		}
	}

	/**
	 * 2024-04-14
	 * @used-by Aoe_Scheduler_Model_Observer::dispatch()
	 */
	function syncSubscriberBatchData():void {hcg_mc_batches_new()->handleSubscriberBatches();}

	function processWebhookData()
	{
		Mage::getModel('mailchimp/processWebhook')->processWebhookData();
	}

	function deleteWebhookRequests()
	{
		Mage::getModel('mailchimp/processWebhook')->deleteProcessed();
	}

	function clearEcommerceData()
	{
		Mage::getModel('mailchimp/clearEcommerce')->clearEcommerceData();
	}
	function clearBatches()
	{
		Mage::getModel('mailchimp/clearBatches')->clearBatches();
	}

	protected function getHelper($type='')
	{
		return $this->_mailChimpHelper;
	}

	protected function getMigrationHelper()
	{
		return $this->_mailChimpMigrationHelper;
	}
}
