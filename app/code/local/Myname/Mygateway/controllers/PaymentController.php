<?php
/*
Mygateway Payment Controller
By: KDI
*/

class Myname_Mygateway_PaymentController extends Mage_Core_Controller_Front_Action {
	// The redirect action is triggered when someone places an order
	public function redirectAction() {
		$this->loadLayout();
        $block = $this->getLayout()->createBlock('Mage_Core_Block_Template','mygateway',array('template' => 'mygateway/redirect.phtml'));
		$this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
	}
	
	// The response action is triggered when your gateway sends back a response after processing the customer's payment
	public function responseAction() {
		if($this->getRequest()->isPost()) {
			
			/*
			/* Your gateway's code to make sure the reponse you
			/* just got is from the gatway and not from some weirdo.
			/* This generally has some checksum or other checks,
			/* and is provided by the gateway.
			/* For now, we assume that the gateway's response is valid
			*/
			
			//$validated = true;
			//$orderId = '123'; // Generally sent by gateway
			
			$validated=0;
			$orderId=$_POST['sessioninfo'];
			$kdigwtoken=$_POST['kdigwtoken'];
			if ($orderId) {
			// validate the order
				$_order = new Mage_Sales_Model_Order();
				$_order->loadByIncrementId($orderId);
				$store=Mage::app()->getStore()->getStoreId();
				$mer_id=Mage::getStoreConfig('payment/mygateway/mer_id',$store);
				$mer_password=Mage::getStoreConfig('payment/mygateway/mer_password',$store);
				$mer_secret=Mage::getStoreConfig('payment/mygateway/mer_secret',$store);
				$amount = $_order->getBaseGrandTotal();
			        $string1 = $mer_secret . $mer_id . $mer_password . $orderId . $amount;
			        $md5result = md5($string1);
			        $md5result = strtoupper($md5result);
				if ($kdigwtoken == $md5result) {
					//token matched expected token so this is a valid success message
					$validated=true;
				}

			}
			

			if($validated) {
				// Payment was successful, so update the order's state, send order email and move to the success page
				$order = Mage::getModel('sales/order');
				$order->loadByIncrementId($orderId);
				$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Gateway has authorized the payment.');
				
				$order->sendNewOrderEmail();
				$order->setEmailSent(true);
				
				$order->save();
			
				Mage::getSingleton('checkout/session')->unsQuoteId();
				
				Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure'=>true));
			}
			else {
				// There is a problem in the response we got
				$this->cancelAction();
				Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
			}
		}
		else
			Mage_Core_Controller_Varien_Action::_redirect('');
	}
	
	// The cancel action is triggered when an order is to be cancelled
	public function cancelAction() {
        if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
            if($order->getId()) {
				// Flag the order as 'cancelled' and save it
				$order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Gateway has declined the payment.')->save();
			}
        }
	}
}
