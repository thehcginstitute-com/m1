<?php
/**
 * Helper class for richpanel properties
 *
 * @author Shubhanshu Chouhan <shubhanshu@richpanel.com>
 */
class Richpanel_Analytics_Helper_Data extends Mage_Core_Helper_Abstract
{
    public $protocol = 'https://';
    public $api_url = 'api.richpanel.com/v1';

    /**
     * Update input object with user details of currently logged In User
     * @param $properties
     * @return array
     */
    public function updateWithUserDetails($customerId = NULL){

        $customer = NULL;
        $data = array();

        if($customerId){
            $customer = Mage::getModel('customer/customer')->load($customerId);
        }
        else if (Mage::app()->isInstalled() && Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
        }

        if($customer){
            $user_properties = $customer->getData();

            $data = array(
                'email' => $customer->getEmail(), 
                'firstName' => $user_properties['firstname'], 
                'lastName' => $user_properties['lastname'],
                'uid'   => $customer->getEmail(),
                'sourceId'   => (int)$customer->getId()
            );
            
            $customerBillingAddressId = $customer->getDefaultBilling();
            if($customerBillingAddressId){
                $address = Mage::getModel('customer/address')->load($customerBillingAddressId);
                $addressData = $address->getData();
                $data['billingAddress'] = array(
                    'firstName'	=> $addressData['firstname'],
                    'lastName'	=> $addressData['lastname'],
                    'city'	=> $addressData['city'],
                    'state'	=> $addressData['region'],
                    'country'	=> $addressData['country_id'],
                    'postcode'	=> $addressData['postcode'],
                    'phone'	=> $addressData['telephone'],
                    'address1'	=> $addressData['street']
                );
            }
    
            $customerShippingAddressId = $customer->getDefaultShipping();
            if($customerShippingAddressId){
                $address = Mage::getModel('customer/address')->load($customerShippingAddressId);
                $addressData = $address->getData();
                $data['shippingAddress'] = array(
                    'firstName'	=> $addressData['firstname'],
                    'lastName'	=> $addressData['lastname'],
                    'city'	=> $addressData['city'],
                    'state'	=> $addressData['region'],
                    'country'	=> $addressData['country_id'],
                    'postcode'	=> $addressData['postcode'],
                    'phone'	=> $addressData['telephone'],
                    'address1'	=> $addressData['street']
                );
            }

        }

        return $data;
    }

    /**
     * If User is not in database, fetch all details from order and create user_properties
     */
    public function updateWithUserDetailsWithOrderData($properties) {
        
        $data = array(
            'uid' => $properties['email'], 
            'email' => $properties['email'], 
            'firstName' => $properties['first_name'], 
            'lastName' => $properties['last_name']
        );
        
        $data['billingAddress'] = array(
            'city'	=> $properties['billing_city'],
            'state'	=> $properties['billing_region'],
            'country'	=> $properties['billing_country'],
            'postcode'	=> $properties['billing_postcode'],
            'phone'	=> $properties['billing_phone'],
            'address1'	=> $properties['billing_address']
        );

        $data['shippingAddress'] = array(
            'city'	=> $properties['shipping_city'],
            'state'	=> $properties['shipping_region'],
            'country'	=> $properties['shipping_country'],
            'postcode'	=> $properties['shipping_postcode'],
            'phone'	=> $properties['shipping_phone'],
            'address1'	=> $properties['shipping_address']
        );

        // $properties['user_properties'] = base64_encode(json_encode($data));
        // $properties['user_properties'] = $data;
        // $properties['is_logged_in'] = True;

        return $data;
    }

    /**
     * Encrypt data
     */
    public function encryptData($data, $storeId = NULL){
        $api_key = $this->getApiToken($storeId);
        $api_secret = $this->getApiSecret($storeId);

		$key256 = substr($api_secret, 0, 32);
		$iv = substr($api_key, 0, 16);

		$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
		
		mcrypt_generic_init($cipher, $key256, $iv);		
		$cipherText256 = mcrypt_generic($cipher,$data);
		
		mcrypt_generic_deinit($cipher);
		$cipherHexText256 =bin2hex($cipherText256);

		// error_log($cipherHexText256);

		return $cipherHexText256;
	}

    /**
     * Get session instance
     *
     * @return Mage_Core_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('core/session');
    }

    /**
    * Get storeId for the current request context
    *
    * @return string
    */
    public function getStoreId($request = null) {
        if ($request) {
            # If request is passed retrieve store by storeCode
            $storeCode = $request->getParam('store');

            if ($storeCode) {
                return Mage::getModel('core/store')->load($storeCode)->getId();
            }
        }

        # If no request or empty store code
        return Mage::app()->getStore()->getId();
    }

    /**
     * Check if richpanel module is enabled
     *
     * @return boolean
     */
    public function isEnabled($storeId = null)
    {
        return Mage::getStoreConfig('richpanel_analytics_settings/settings/enable', $storeId);
    }

    /**
     * Get API Token from system configuration
     *
     * @return string
     */
    public function getApiToken($storeId = null)
    {
        return Mage::getStoreConfig('richpanel_analytics_settings/settings/api_key', $storeId);
    }

    /**
     * Get API Secret from system configuration
     *
     * @return string
     */
    public function getApiSecret($storeId = null)
    {
        return Mage::getStoreConfig('richpanel_analytics_settings/settings/api_secret', $storeId);
    }

    /**
     * Add event to queue
     *
     * @param string $method Can be identify|track
     * @param string $type
     * @param string|array $data
     */
    public function addEvent($method, $type, $data, $userProperties = NULL, $customerId = NULL)
    {
        $events = array();

        if ($this->getSession()->getData(Richpanel_Analytics_Block_Head::DATA_TAG) != '') {
            $events = (array)$this->getSession()->getData(Richpanel_Analytics_Block_Head::DATA_TAG);
        }

        if ($customerId) {
            $userProperties = $this->updateWithUserDetails($customerId);
        } else {
            $tempData = $this->updateWithUserDetails();
            if ($tempData) {
                $userProperties = $tempData;
            }
        }

        $eventToAdd = array(
            'method' => $method,
            'type' => $type
        );

        if ($data) {
            $eventToAdd['properties'] = $data;
        }

        if ($userProperties) {
            $eventToAdd['userProperties'] = $userProperties;
        }

        if ($method == 'identify') {
            array_unshift($events, $eventToAdd);
        } else {
            array_push($events, $eventToAdd);
        }

        $this->getSession()->setData(Richpanel_Analytics_Block_Head::DATA_TAG, $events);
    }

    private function orderPaymentMethod($order) {
        try {
            return $order->getPayment()->getMethodInstance()->getTitle();
        // For the cases when the payment method was deleted.
        } catch (Exception $e) {
            return $order->getPayment()->method;
        }
    }

    public function getProductImages($productId)
    {
        $gallery_images = Mage::getModel('catalog/product')->load($productId)->getMediaGalleryImages();

        $items = array();

        foreach($gallery_images as $g_image) {
            $items[] = $g_image['url'];
        }
        return $items;
    }

    public function getCategoryParent($categoryId) {
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $catgoriy_result = array();
        foreach ($category->getParentCategories() as $parent) {
            $catgoriy_result[] = array(
                'id' => $parent->getId(),
                'name' => $parent->getName()
            );
        }
        return $catgoriy_result;
    }

    private function getProductDetails($item) {
        $product = $item->getProduct();
        $details = array(
            'id'    => (string)$item->getProductId(),
            'sku'   => $product->getSku(),
            'price' => $item->getPrice(),
            'name'  => $item->getName()
        );
        // For the cases when the image was deleted.
        try {
            $images = $this->getProductImages((string)$item->getProductId());
            if ($images) {
                $details['image_url'] = $images;
            }
        } catch (Exception $e) {}
        try {
            if (count($product->getCategoryIds())) {
                $categories = array();
                $collection = $product->getCategoryCollection()->addAttributeToSelect('*');
                foreach ($collection as $category) {
                    $categories[] = array(
                        'id' => $category->getId(),
                        'name' => $category->getName(),
                        'parent' => $this->getCategoryParent($category->getId())
                    );
                }
                $details['categories'] = $categories;
            }
        } catch (Exception $e) {}

        return $details;
    }

    private function getItems($order) {
        $items = array();
        foreach ($order->getAllVisibleItems() as $item) {
            // Main product attributes
            $parentItemDetails = $this->getProductDetails($item);
            $parentItemDetails['quantity'] = $item->getQtyOrdered();
            $parentItemDetails['url'] = $item->getProduct()->getProductUrl();
            $childrenItems = $item->getChildrenItems();
            if (count($childrenItems) > 0) {
                foreach ($childrenItems as $childItem) {
                    $childProductDetails = $this->getProductDetails($childItem);
                    // For legacy reasons we are passing the child SKU as an identifier
                    $optionId = ($childProductDetails['sku']) ? $childProductDetails['sku'] : $childProductDetails['id'];
                    $childItemEntry = array_merge($parentItemDetails, array(
                        'option_id'    => $optionId,
                        'option_sku'   => $childProductDetails['sku'],
                        'option_price' => $parentItemDetails['price'],
                        'option_name'  => $childProductDetails['name'],
                        'option_image_url' => $childProductDetails['image_url']
                    ));
                    array_push($items, array_filter($childItemEntry));
                }
            } else {
                array_push($items, array_filter($parentItemDetails));
            }
        }
        return $items;
    }

    /**
     * Get order details and sort them for richpanel
     *
     * @param  Mage_Sales_Model_Order $order
     * @return array
     */
    public function prepareOrderDetails($order, $context, $storeId = NULL)
    {
        if ($context == null)
            $context = "import";

        $data = array(
            'order_id'          => $order->getIncrementId(),
            'order_status'      => $order->getStatus(),
            'amount'            => (float)$order->getGrandTotal(),
            'shipping_amount'   => (float)$order->getShippingAmount(),
            'tax_amount'        => $order->getTaxAmount(),
            'items'             => array(),
            'shipping_method'   => $order->getShippingDescription(),
            'payment_method'    => $this->orderPaymentMethod($order),
            'context'           => $context
        );

        // Custom Fields
        if ($order->getData('canal')) {
            $data['canal'] = $order->getData('canal');
        }

        $this->_assignBillingInfo($data, $order);

        if ($order->getCouponCode()) {
            $data['coupons'] = array($order->getCouponCode());
        }

        $data['items'] = $this->getItems($order);

        return $data;
    }
    
    /**
     * Create HTTP request to Richpanel API to sync multiple orders
     *
     * @param Array(Mage_Sales_Model_Order) $orders
     * @return void
     */
    public function callBatchApi($storeId, $orders, $async = true, $event_type)
    {
        try {
            if($event_type == 'send_batch'){
                $ordersForSubmition = $this->_buildOrdersForSubmition($orders, "import", $storeId);
            }else if($event_type == 'order_status_update'){
                $ordersForSubmition = $this->_buildOrdersForSubmition($orders, "status_change", $storeId);
            }
            
            $call = $this->_buildCall($storeId, $ordersForSubmition, 'send_batch');

            $this->_callRichpanelApi($storeId, $call, $async);
            Mage::log("Calling callBatchApi");
        } catch (Exception $e) {
            Mage::log("Execption callBatchApi");
            Mage::log($e->getMessage(), null, 'Richpanel_Analytics.log');
        }
    }

    private function _callRichpanelApi($storeId, $call, $async = true) {
        ksort($call);

        $basedCall = base64_encode(Mage::helper('core')->jsonEncode($call));
        $signature = md5($basedCall.$this->getApiSecret($storeId));

        $requestBody = array(
            's'   => $signature,
            'hs'  => $basedCall
        );

        // /** @var Richpanel_Analytics_Helper_Asynchttpclient $asyncHttpHelper */
        // $this->apiCall('https://api.richpanel.com/v1/bt', $requestBody);
        // Mage::log("Calling _callRichpanelApi");

        $helper = Mage::helper('richpanel_analytics');

        if($helper->isEnabled($storeId)) {
            // $this->apiCall('https://api.richpanel.com/v1/bt', $requestBody);
            $this->apiCall($this->protocol.$this->api_url.'/bt', $requestBody);
            Mage::log("Calling _callRichpanelApi");
        }
    }

    private function apiCall($host, $requestBody){

        $iClient = new Varien_Http_Client();
        $iClient->setUri($host)
            ->setMethod('POST')
            ->setConfig(array(
                    'maxredirects'=>0,
                    'timeout'=>30,
            ));
        $iClient->setRawData(json_encode($requestBody), "application/json;charset=UTF-8");    

        try{
            $response = $iClient->request();
            if ($response->isSuccessful()) {
                Mage::log($response->getBody());
            }
        } catch (Exception $e) {
            Mage::log($e);
        }

    }

    /**
     * Create submition ready arrays from Array of Mage_Sales_Model_Order
     * @param Array(Mage_Sales_Model_Order) $orders
     * @return Array of Arrays
     */
    private function _buildOrdersForSubmition($orders, $context, $storeId = NULL) {
        $ordersForSubmition = array();

        foreach ($orders as $order) {
            if ($order->getId()) {
                array_push($ordersForSubmition, $this->_buildOrderForSubmition($order, $context, $storeId));
            }
        }

        return $ordersForSubmition;
    }

    /**
     * Build event array ready for encoding and encrypting. Built array is returned using ksort.
     *
     * @param  string  $ident
     * @param  string  $event
     * @param  array  $properties
     * @param  boolean|array $identityData
     * @param  boolean|int $time
     * @param  boolean|array $callParameters
     * @return void
     */
    private function _buildEventArray($ident, $event, $properties, $identityData = false, $time = false, $callParameters = false)
    {
        $call = array(
            'event'    => $event,
            'properties'        => $properties
            // 'uid'           => $ident
        );

        if($time){
            $call['time'] = array('originalTimestamp' => $time, 'sentAt' => round(microtime(true) * 1000));
        } else {
            $call['time'] = array('sentAt' => round(microtime(true) * 1000));
        }

        // check for special parameters to include in the API call
        if($callParameters){
            if($callParameters['ip']){
                $ipContext = array('networkIP' => $callParameters['ip']);
                $call['context'] = array('ip' => $ipContext);
            }
            if($callParameters['userProperties']){
                $call['userProperties'] = $callParameters['userProperties'];
            }
        }

        ksort($call);

        return $call;
    }

    private function _buildOrderForSubmition($order, $context, $storeId = NULL) {
        $orderDetails = $this->prepareOrderDetails($order, $context, $storeId);
        // initialize additional properties
        $callParameters = array();
        // check if order has customer IP in it
        $ip = $order->getRemoteIp();
        if ($ip) {
            $callParameters['ip'] = $ip;
        }
        // initialize time
        $time = false;
        if ($order->getCreatedAtStoreDate()) {
            $time = $order->getCreatedAtStoreDate()->getTimestamp() * 1000;
        }

        $customerId = $order->getCustomerId();
        if($customerId){
            $callParameters['userProperties'] = $this->updateWithUserDetails($customerId);
        } else {
            // Guest User, Get user data from order if customer id is not present
            $callParameters['userProperties'] = $this->updateWithUserDetailsWithOrderData($orderDetails);
        }

        $identityData = $this->_orderIdentityData($order);

        if ($context == "status_change") {
            return $this->_buildEventArray(
                $identityData['id'], 'order_status_update', $orderDetails, $identityData, $time, $callParameters
            );    
        }else if  ($context == "import") {
            return $this->_buildEventArray(
                $identityData['id'], 'order', $orderDetails, $identityData, $time, $callParameters
            );
        }

        return $this->_buildEventArray(
            $identityData['id'], 'order', $orderDetails, $identityData, $time, $callParameters
        );
    }


    private function _orderIdentityData($order) {
        return array(
            'id'            => $order->getCustomerId(),
            'email'         => $order->getCustomerEmail(),
            'first_name'    => $order->getBillingAddress()->getFirstname(),
            'last_name'     => $order->getBillingAddress()->getLastname(),
            'name'          => $order->getBillingAddress()->getName(),
        );
    }

    private function _buildCall($storeId, $ordersForSubmition, $event_type) {
        return array(
            'appClientId'    => $this->getApiToken($storeId),
            'events'   => $ordersForSubmition,
            // for debugging/support purposes
            'platform' => 'Magento ' . Mage::getEdition() . ' ' . Mage::getVersion(),
            'version'  => (string)Mage::getConfig()->getModuleConfig("Richpanel_Analytics")->version,
            'event' => $event_type
        );
    }

    private function _assignBillingInfo(&$data, $order)
    {
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();
        # Assign billing data to order data array
        $data['billing_phone']    = $billingAddress->getTelephone();
        $data['billing_country']  = $billingAddress->getCountryId();
        $data['billing_region']   = $billingAddress->getRegion();
        $data['billing_city']     = $billingAddress->getCity();
        $data['billing_postcode'] = $billingAddress->getPostcode();
        $data['billing_address']  = $billingAddress->getStreetFull();
        $data['billing_company']  = $billingAddress->getCompany();

        if ($shippingAddress) {
            $data['shipping_phone']    = $shippingAddress->getTelephone();
            $data['shipping_country']  = $shippingAddress->getCountryId();
            $data['shipping_region']   = $shippingAddress->getRegion();
            $data['shipping_city']     = $shippingAddress->getCity();
            $data['shipping_postcode'] = $shippingAddress->getPostcode();
            $data['shipping_address']  = $shippingAddress->getStreetFull();
            $data['shipping_company']  = $shippingAddress->getCompany();
        }

        $data['email']             = $order->getCustomerEmail();
        $data['first_name']        = $billingAddress->getFirstname();
        $data['last_name']         = $billingAddress->getLastname();
        $data['name']              = $billingAddress->getName();

    }
}
