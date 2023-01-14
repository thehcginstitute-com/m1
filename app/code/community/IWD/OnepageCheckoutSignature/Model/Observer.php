<?php
class IWD_OnepageCheckoutSignature_Model_Observer
{
	public function addSignature($observer)
	{
		$request = Mage::app()->getRequest();
		$signature_name = '';
		$signature_json = '';
		$lastOrderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		$lastOrderId = Mage::getModel('sales/order')
		->loadByIncrementId($lastOrderId)
		->getEntityId();
		$name = Mage::app()->getRequest()->getPost('signature_name');
		$json =Mage::app()->getRequest()->getPost('output');
	
		$orders = Mage::getModel('sales/order')->getCollection()
		->setOrder('created_at','DESC')
		->setPageSize(1)
		->setCurPage(1);
	
		$lastOrderId = $orders->getFirstItem()->getEntityId();
	
		if($name!='')
				$signature_name = $name;
		if($json)
			$signature_json = $json;
		
		$model = Mage::getModel('opcsignature/signaturer');
		try
		{
			$model->setOrderId($lastOrderId);
			$model->setSignatureName($signature_name);
			$model->setSignatureJson($signature_json);
			$model->save();
		}
		catch(Exception $e)
		{
			Mage::log('opc signature - '.$e);
		}
	}
	public function checkRequiredModules($observer){
		$cache = Mage::app()->getCache();
		if (Mage::getSingleton('admin/session')->isLoggedIn()) {
			if (!Mage::getConfig()->getModuleConfig('IWD_All')->is('active', 'true')){
				if ($cache->load("opcsignature")===false){
					$message = 'Important: Please setup IWD_ALL in order to finish <strong>IWD Signature At Checkout</strong>  installation.<br />
Please download <a href="http://iwdextensions.com/media/modules/iwd_all.tgz" target="_blank">IWD_ALL</a> and setup it via Magento Connect.<br />
Please refer to <a href="https://docs.google.com/document/d/1hnePQ-aqcUh79Q6f4FHeoCNHl1KCt7ft2V-3Y6e1NEs/edit?usp=sharing" target="_blank">installation guide</a>';
					Mage::getSingleton('adminhtml/session')->addNotice($message);
					$cache->save('true', 'opcsignature', array("opcsignature"), $lifeTime=5);
				}
			}
		}
	}
}