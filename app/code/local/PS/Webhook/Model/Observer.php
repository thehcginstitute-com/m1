<?php

class PS_Webhook_Model_Observer
{
	function sendOrderToWebhook(Varien_Event_Observer $observer)
	{
		$order = $observer->getEvent()->getOrder();
		$this->processOrder($order);
	}

	function processOrder($order)
	{
		$data = [
			'customer_firstname' => $order->getCustomerFirstname(),
			'customer_lastname' => $order->getCustomerLastname(),
			'customer_email' => $order->getCustomerEmail(),
			'billing_telephone' => $order->getBillingAddress()->getTelephone(),
			'shipping_region' => $order->getShippingAddress()->getRegion(),
			'items' => [],
		];

		foreach ($order->getAllItems() as $item) {
			if (stripos($item->getName(), 'hcg') !== false) {
				$data['items'][] = [
					'item_name' => $item->getName(),
					'item_price' => $item->getPrice(),
					'order_id' => $order->getIncrementId(),
				];
			}
		}

		if (!empty($data['items'])) {
			$webhookResult = $this->sendToWebhook($data);
			Mage::log("Order " . $order->getIncrementId() . " sent. Data " . json_encode($data), null, 'orders-make.log', true);
		} else {
			Mage::log("Order " . $order->getIncrementId() . " skipped.", null, 'orders-make.log', true);
		}
	}

	protected function sendToWebhook($data)
	{
		$domain = Mage::helper('ps_webhook')->getDomain();
		$url = "https://hook.us1.make.com/kypcfu1qtvdqgqybh7fbh2elk8ofllsy?from=$domain";

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json'
		]);

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}
}
