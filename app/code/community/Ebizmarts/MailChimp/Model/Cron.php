<?php
# 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
use Ebizmarts_MailChimp_Model_ProcessWebhook as Process;
class Ebizmarts_MailChimp_Model_Cron
{
	/**
	 * @var Ebizmarts_MailChimp_Helper_Data
	 */
	protected $_mailChimpHelper;

	function __construct()
	{
		$this->_mailChimpHelper = hcg_mc_h();
	}

	function syncEcommerceBatchData() {\HCG\MailChimp\Batch\Commerce::p();}

	/**
	 * 2024-04-14
	 * @used-by Aoe_Scheduler_Model_Observer::dispatch()
	 */
	function syncSubscriberBatchData():void {\HCG\MailChimp\Batch\Subscriber::p();}

	function processWebhookData() {
		$p = new Process; /** @var Process $p */
		$p->processWebhookData();
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
}
