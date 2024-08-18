<?php
require_once 'abstract.php';

class Mage_Shell_Webhook extends Mage_Shell_Abstract
{
	public function run()
	{
		$customerId = $this->getArg('customer_id');

		if (!$customerId) {
			echo "Please provide a customer ID using --customer_id\n";
			return;
		}

		$customer = Mage::getModel('customer/customer')->load($customerId);

		if (!$customer->getId()) {
			echo "Customer with ID $customerId does not exist.\n";
			return;
		}

		$observer = Mage::getModel('ps_webhook/customerObserver');
		$observer->sendToWebhook($customer);

		echo "Webhook sent for customer ID $customerId.\n";
	}
}

$shell = new Mage_Shell_Webhook();
$shell->run();
