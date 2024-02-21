<?php

class Pma_Importer_Block_Adminhtml_Settings extends Mage_Adminhtml_Block_Template {

	protected $response = array();

	 function __construct() {
		parent::__construct();
		$importerModel = Mage::getModel('importer/importer');
		$collection = $importerModel->getCollection();

		 foreach($collection as $item):

			$orderData = unserialize($item->getOrders_type());

			if(isset($orderData)):

				if(isset($orderData)):
				 $response =  $this->_orderfilterAction($orderData);
				else:
				  Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Select Order Filter Option'));
				endif;

				$jsonData = Mage::helper('core')->jsonEncode($response);

				$this->_curlDataAction($jsonData,$item->getWebservice_url());

			endif;

			$this->assign('webserviceUrl', $item->getWebservice_url());
			$this->assign('accountToken', $item->getAccount_token());
			$this->assign('orderTypes', $item->getOrders_type());

		  /*$this->assign('ordersCollection', $this->_orderfilterAction($orderData));
			$this->assign('jsonData',$jsonData);*/



			$this->setTemplate('importer/settings.phtml');

		  endforeach;


	}

	 /**
	 * Filter the order based on checked box selected by admin.
	 */

	 protected function _orderfilterAction($orderData){

		  $collection = Mage::getModel('sales/order')->getCollection()->addAttributeToSort('entity_id', 'desc')->setPage(0,10);

		  if(count($orderData) == 1):

			 // get collection of order that placed by admin.
			 if(in_array('adminOrder',$orderData)):
			   $collection->addAttributeToFilter('remote_ip', array('null'=>'NULL'));
			 endif;

			 // get collection of order that placed by customer.
			 if(in_array('customerOrder',$orderData)):
			   $collection->addAttributeToFilter('remote_ip', array('neq'=>'NULL'));
			 endif;

			$response = $this->_orderencodeAction($collection);

		   endif;

			// get collection of order that placed by customer and admin both.
			if(count($orderData) > 1):
			  $response = $this->_orderencodeAction($collection);
			endif;

			return $response ;
	  }
	
  
	 function _orderencodeAction($collection){

			   $importerModel = Mage::getModel('importer/importer');
			   $dataCollection = $importerModel->getCollection();
			   foreach($dataCollection as $item){
					$response['account_token']= $item->getAccount_token();
			   }

		  foreach($collection as $items){

			   $order = Mage::getModel('sales/order')->loadByIncrementId($items->getIncrement_id());
			   $customer  = Mage::getModel('customer/customer')->load($items->getCustomerId());
 
			   $response[$items->getIncrement_id()]['grand_total'] = $items->getGrand_total();
			   $response[$items->getIncrement_id()]['visitor_id'] =  $customer->getData('visitorid');
			   //$response[$items->getIncrement_id()]['transaction_id'] =  $order->getPayment()->getLastTransId();

			   $response[$items->getIncrement_id()]['shipping'] = $items->getShipping_amount();
			   $response[$items->getIncrement_id()]['tax'] = $items->getTax_amount();
			   $response[$items->getIncrement_id()]['order_status'] = $items->getStatusLabel();

			   $orderItems = $order->getAllVisibleItems();

			   foreach ($orderItems as $item) {

				  $response[$items->getIncrement_id()]['product_id'][$item->getProductId()]['name'] = $item->getName();
				  $response[$items->getIncrement_id()]['product_id'][$item->getProductId()]['sku']  = $item->getSku();
				  $response[$items->getIncrement_id()]['product_id'][$item->getProductId()]['price'] = $item->getPrice();

				  $_product=Mage::getModel('catalog/product')->load($item->getProductId());

				  if($_product->getTypeId() == 'simple'){
					 $response[$items->getIncrement_id()]['product_id'][$item->getProductId()]['qty']  = $item->getQtyOrdered();
				  } else {
					$response[$items->getIncrement_id()]['product_id'][$item->getProductId()]['qty']  = $item->getQtyOrdered();
				  }

				  $product = Mage::getModel('catalog/product')->load($item->getProductId());
				  $cats = $product->getCategoryIds();
				  $category_name = '';
				  foreach($cats as $cat){
					 $category_id = $cat; // just grab the first id
					 $category = Mage::getModel('catalog/category')->load($category_id);
					 $category_name .= $category->getName().'&sol;';
				   }
				 $response[$items->getIncrement_id()]['product_id'][$item->getProductId()]['category']  = $category_name ;




			   }
		  }

		  return $response ;
	 }

	   function _curlDataAction($json,$url){

			$post_data = $json ;

			//echo "<pre>"; print_r($post_data);
			//die('<--');

			$headers = array (
					 "POST HTTP/1.0",
					 "Content-type: text/xml;application/json;charset=\"utf-8\"",
					 "Accept: text/xml",
					 "Cache-Control: no-cache",
					 "Pragma: no-cache",
					 "SOAPAction: \"transactions\"",
					 "Content-length: " . strlen ( $post_data )
			 );

			 $ch = curl_init ();
			 curl_setopt ( $ch, CURLOPT_URL, $url );
			 curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
			 curl_setopt ( $ch, CURLOPT_TIMEOUT, 60 );
			 curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
			 curl_setopt ( $ch, CURLOPT_USERAGENT, $_SERVER ['HTTP_USER_AGENT'] );
			 curl_setopt ( $ch, CURLOPT_POST, 1 );
			 curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );

			 $data = curl_exec ( $ch );

			if (curl_errno ( $ch )) {
				 print "Error: " . curl_error ( $ch );
			 }
			 else {
				 curl_close ( $ch );
			 }

	  }

}
