<?php
class PS_Webhook_Model_CustomerObserver
{
	public function sendWebhook(Varien_Event_Observer $observer)
	{
		$customer = $observer->getEvent()->getCustomer();
		if (!$customer->getOrigData('entity_id')) {
			$this->sendToWebhook($customer);
		}
	}

	public function sendToWebhook($customer)
	{
		$data = array(
			'first_name' => $customer->getFirstname(),
			'last_name' => $customer->getLastname(),
			'email' => $customer->getEmail()
		);

		$domain = Mage::helper('ps_webhook')->getDomain();
		$url = "https://hook.us1.make.com/inxmxmzu6ro6q0hsscd8kq2s49hibgbn?from=$domain";

		$client = new Varien_Http_Client($url);
		$client->setMethod(Zend_Http_Client::POST);
		$client->setRawData(json_encode($data), 'application/json');
		$response = $client->request();

		if (!$response->isError()) {
			Mage::log('Customer '. $customer->getEmail() . " sent", null, 'customers-make.log');
		} else {
			Mage::log('Customer '. $customer->getEmail() . "is not sent " . $response->getBody(), null, 'customers-make.log');
		}
	}
}
