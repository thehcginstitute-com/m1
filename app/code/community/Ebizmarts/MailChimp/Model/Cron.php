<?php
# 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
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

	/**
	 * 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_ProcessWebhook`": https://github.com/thehcginstitute-com/m1/issues/590
	 * @used-by Aoe_Scheduler_Model_Observer::dispatch() (app/code/community/Ebizmarts/MailChimp/etc/config.xml)
	 */
	function deleteProcessed():void {
		$helper = hcg_mc_h();
		$resource = $helper->getCoreResource();
		$connection = $resource->getConnection('core_write');
		$tableName = $resource->getTableName('mailchimp/webhookrequest');
		$where = ["fired_at < NOW() - INTERVAL 30 DAY AND processed = 1"];
		$connection->delete($tableName, $where);
	}

	function syncEcommerceBatchData() {\HCG\MailChimp\Batch\Commerce::p();}

	/**
	 * 2024-04-14
	 * @used-by Aoe_Scheduler_Model_Observer::dispatch()
	 */
	function syncSubscriberBatchData():void {\HCG\MailChimp\Batch\Subscriber::p();}

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
