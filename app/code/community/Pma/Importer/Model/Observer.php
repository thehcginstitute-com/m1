<?php

class Pma_Importer_Model_Observer {

   function customerDataUpdate($observer){
   
   //echo "<pre>"; print_r($_POST);
   echo  $observer->getEvent()->getData('order');
	  die('<--');

  }
  
    function status(Varien_Event_Observer $observer) {

	//echo "<pre>"; print_r($_POST);

	  $data = Mage::app()->getRequest()->getPost('order');
	  $visitorId = $data['account']['visitorid'];


	 $order = $observer->getEvent()->getOrder();


			   $response = array();

			   $importerModel = Mage::getModel('importer/importer');
			   $dataCollection = $importerModel->getCollection();

			   foreach($dataCollection as $item){
					$response['account_token']= $item->getAccount_token();
			   }

			   $order = Mage::getModel('sales/order')->loadByIncrementId($order->getIncrement_id());
			   $customer  = Mage::getModel('customer/customer')->load($order->getCustomerId());

			   if($visitorId != $customer->getData('visitorid'))
			   {
				   $customer->setData('visitorid',$visitorId);
				   $customer->save();
			   }

			   $response[$order->getIncrement_id()]['grand_total'] = $order->getGrand_total();
			   $response[$order->getIncrement_id()]['visitor_id'] = $customer->getData('visitorid');
			   $response[$order->getIncrement_id()]['shipping'] = $order->getShipping_amount();
			   $response[$order->getIncrement_id()]['tax'] = $order->getTax_amount();

			   $response[$order->getIncrement_id()]['order_status'] = $order->getStatusLabel();




				$response[$order->getIncrement_id()]['Transaction_id'] =  $order->getPayment()->getLastTransId();
				$orderItems = $order->getAllVisibleItems();

			   foreach ($orderItems as $item) {

				  //$response[$order->getIncrement_id()][$item->getProductId()]['id']   = $item->getProductId();
				  $response[$order->getIncrement_id()]['product_id'][$item->getProductId()]['name'] = $item->getName();
				  $response[$order->getIncrement_id()]['product_id'][$item->getProductId()]['sku']  = $item->getSku();
				  $response[$order->getIncrement_id()]['product_id'][$item->getProductId()]['price'] = $item->getPrice();

				  $_product=Mage::getModel('catalog/product')->load($item->getProductId());
				  $response[$order->getIncrement_id()]['product_id'][$item->getProductId()]['type']= $_product->getTypeId();

				  if($_product->getTypeId() == 'simple'){
					 $response[$order->getIncrement_id()]['product_id'][$item->getProductId()]['qty']  = $item->getQtyOrdered();
				  } else {
					$response[$order->getIncrement_id()]['product_id'][$item->getProductId()]['qty']  = $item->getQtyOrdered();
				  }

				  $product = Mage::getModel('catalog/product')->load($item->getProductId());
				  $cats = $product->getCategoryIds();
				  $category_name = '';
				  foreach($cats as $cat){
					 $category_id = $cat; // just grab the first id
					 $category = Mage::getModel('catalog/category')->load($category_id);
					 $category_name .= $category->getName().'&sol;';
				   }
				 $response[$order->getIncrement_id()]['product_id'][$item->getProductId()]['category']  = $category_name ;

			   }

				  $url = "https://ga-phonetracking.appspot.com/api/magento/v1/import/transactions";
				  $post_data =  Mage::helper('core')->jsonEncode($response);


				   $headers = array (
							"POST HTTP/1.0",
							"Content-type: text/xml;application/json;charset=\"utf-8\"",
							"Accept: text/xml",
							"Cache-Control: no-cache",
							"Pragma: no-cache",
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
						 Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__("Error: " . curl_error ( $ch )));
					} else {
					   // var_dump ( $data );
						curl_close ( $ch );

					}



   }

}
