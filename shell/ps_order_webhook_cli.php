<?php

require_once 'abstract.php';

class PS_Webhook_Cli extends Mage_Shell_Abstract
{
	public function run()
	{
		$orderId = $this->getArg('order_id');
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

		if (!$order->getId()) {
			echo "Order $orderId not found.\n";
			return;
		}

		echo "Order $orderId found.\n";

		$observer = Mage::getModel('ps_webhook/observer');
		$observer->processOrder($order);
	}
}

$shell = new PS_Webhook_Cli();
$shell->run();
