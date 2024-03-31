<?php
# 2024-03-22 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
use Ebizmarts_MailChimp_Model_Api_Products as ApiProducts;
use Ebizmarts_MailChimp_Model_Ecommercesyncdata as SyncD;
use Mage_Sales_Model_Quote as Q;
use Mage_Sales_Model_Quote_Item as QI;
use Mage_Sales_Model_Resource_Quote_Collection as QC;
class Ebizmarts_MailChimp_Model_Api_Carts extends Ebizmarts_MailChimp_Model_Api_ItemSynchronizer
{
	const BATCH_LIMIT = 100;

	protected $_firstDate;
	protected $_counter;
	protected $_batchId;

	protected $_api = null;
	protected $_token = null;

	/**
	 * @var $_ecommerceQuotesCollection Ebizmarts_MailChimp_Model_Resource_Ecommercesyncdata_Quote_Collection
	 */
	protected $_ecommerceQuotesCollection;


	/**
	 * @return array
	 */
	function createBatchJson()
	{
		$mailchimpStoreId = $this->getMailchimpStoreId();
		$magentoStoreId = $this->getMagentoStoreId();

		$this->_ecommerceQuotesCollection = $this->initializeEcommerceResourceCollection();
		$this->_ecommerceQuotesCollection->setStoreId($magentoStoreId);
		$this->_ecommerceQuotesCollection->setMailchimpStoreId($mailchimpStoreId);

		$helper = $this->getHelper();
		$oldStore = $helper->getCurrentStoreId();
		$helper->setCurrentStore($magentoStoreId);

		$allCarts = array();

		if (!$helper->isAbandonedCartEnabled($magentoStoreId)) {
			return $allCarts;
		}

		$dateHelper = $this->getDateHelper();
		$this->_firstDate = $helper->getAbandonedCartFirstDate($magentoStoreId);
		$this->setCounter(0);

		$date = $dateHelper->getDateMicrotime();
		$this->setBatchId(
			'storeid-'
			. $magentoStoreId . '_'
			. Ebizmarts_MailChimp_Model_Config::IS_QUOTE . '_'
			. $date
		);

		$resendTurn = $helper->getResendTurn($magentoStoreId);

		if (!$resendTurn) {
			// get all the carts converted in orders (must be deleted on mailchimp)
			$allCarts = array_merge($allCarts, $this->_getConvertedQuotes());
			// get all the carts modified but not converted in orders
			$allCarts = array_merge($allCarts, $this->_getModifiedQuotes());
		}

		// get new carts
		$allCarts = array_merge($allCarts, $this->_getNewQuotes());
		$helper->setCurrentStore($oldStore);

		return $allCarts;
	}

	/**
	 * @return array
	 */
	function _getConvertedQuotes()
	{
		$mailchimpStoreId = $this->getMailchimpStoreId();

		$batchId = $this->getBatchId();
		$allCarts = array();

		$convertedCarts = $this->buildEcommerceCollectionToSync(
			Ebizmarts_MailChimp_Model_Config::IS_QUOTE,
			"m4m.mailchimp_sync_deleted = 0",
			"converted"
		);

		foreach ($convertedCarts as $cart) {
			$cartId = $cart->getEntityId();
			// we need to delete all the carts associated with this email
			$allCartsForEmail = $this->getAllCartsByEmail($cart->getCustomerEmail());

			foreach ($allCartsForEmail as $cartForEmail) {
				$alreadySentCartId = $cartForEmail->getEntityId();
				$counter = $this->getCounter();

				if ($alreadySentCartId != $cartId) {
					$allCarts[$counter]['method'] = 'DELETE';
					$allCarts[$counter]['path'] = '/ecommerce/stores/'
						. $mailchimpStoreId . '/carts/'
						. $alreadySentCartId;
					$allCarts[$counter]['operation_id'] = $batchId . '_' . $alreadySentCartId;
					$allCarts[$counter]['body'] = '';

					$this->markSyncDataAsDeleted($alreadySentCartId);
					$this->setCounter($this->getCounter() + 1);
				}
			}

			$allCartsForEmail->clear();
			$counter = $this->getCounter();
			$allCarts[$counter]['method'] = 'DELETE';
			$allCarts[$counter]['path'] = '/ecommerce/stores/' . $mailchimpStoreId . '/carts/' . $cartId;
			$allCarts[$counter]['operation_id'] = $batchId . '_' . $cartId;
			$allCarts[$counter]['body'] = '';

			$this->markSyncDataAsDeleted($cartId);
			$this->setCounter($this->getCounter() + 1);
		}

		return $allCarts;
	}

	/**
	 * @return array
	 */
	function _getModifiedQuotes()
	{
		$mailchimpStoreId = $this->getMailchimpStoreId();
		$magentoStoreId = $this->getMagentoStoreId();

		$helper = $this->getHelper();
		$batchId = $this->getBatchId();

		$modifiedCarts = $this->buildEcommerceCollectionToSync(
			Ebizmarts_MailChimp_Model_Config::IS_QUOTE,
			"m4m.mailchimp_sync_deleted = 0 AND m4m.mailchimp_sync_delta < updated_at",
			"modified"
		); /** @var QC $modifiedCarts */

		$allCarts = array();
		foreach ($modifiedCarts as $cart) { /** @var Q $cart */
			$cartId = $cart->getEntityId();
			/**
			 * @var $customer Mage_Customer_Model_Customer
			 */
			$customer = $this->getCustomerModel();
			$customer->setWebsiteId($this->getWebSiteIdFromMagentoStoreId($magentoStoreId));
			$cartCustomerEmail = $cart->getCustomerEmail();
			$customer->loadByEmail($cartCustomerEmail);

			$customerEmail = $customer->getEmail();
			if ($customerEmail != $cartCustomerEmail) {
				$allCartsForEmail = $this->getAllCartsByEmail($cartCustomerEmail);

				foreach ($allCartsForEmail as $cartForEmail) {
					$alreadySentCartId = $cartForEmail->getEntityId();
					$counter = $this->getCounter();

					if ($alreadySentCartId != $cartId) {
						$allCarts[$counter]['method'] = 'DELETE';
						$allCarts[$counter]['path'] = '/ecommerce/stores/'
							. $mailchimpStoreId
							. '/carts/'
							. $alreadySentCartId;
						$allCarts[$counter]['operation_id'] = $batchId . '_' . $alreadySentCartId;
						$allCarts[$counter]['body'] = '';

						$this->markSyncDataAsDeleted($cartId);
						$this->setCounter($counter + 1);
					}
				}

				$allCartsForEmail->clear();
			}

			// avoid carts abandoned as guests when customer email associated to a registered customer.
			if (!$cart->getCustomerId() && $customerEmail == $cartCustomerEmail) {
				$this->addSyncData($cartId);
				continue;
			}

			// send the products that not already sent
			$allCarts = $this->addProductNotSentData($cart, $allCarts);
			$cartJson = $this->makeCart($cart, true);

			if ($cartJson !== false) {
				if (!empty($cartJson)) {
					$helper->modifyCounterSentPerBatch(Ebizmarts_MailChimp_Helper_Data::QUO_MOD);

					$counter = $this->getCounter();
					$allCarts[$counter]['method'] = 'PATCH';
					$allCarts[$counter]['path'] = '/ecommerce/stores/' . $mailchimpStoreId . '/carts/' . $cartId;
					$allCarts[$counter]['operation_id'] = $batchId . '_' . $cartId;
					$allCarts[$counter]['body'] = $cartJson;
					$this->setCounter($this->getCounter() + 1);

					$this->addSyncDataToken($cartId, $this->getToken());
				} else {
					# 2024-03-22 Dmitrii Fediuk https://upwork.com/fl/mage2pro
					# "[`Ebizmarts_MailChimp`] The phrase
					# «There is not supported products in this cart» is grammatically incorrect":
					# https://github.com/thehcginstitute-com/m1/issues/522
					$error = $helper->__('There are not supported products in this cart.');
					$this->addSyncDataError($cartId, $error);
				}
			} else {
				$jsonErrorMessage = json_last_error_msg();
				$this->addSyncDataError($cartId, $jsonErrorMessage, $this->getToken());

				//json encode failed
				$this->logSyncError(
					$jsonErrorMessage,
					Ebizmarts_MailChimp_Model_Config::IS_QUOTE,
					$magentoStoreId,
					'magento_side_error',
					'Json Encode Failure',
					0,
					$cart->getId(),
					0
				);
			}

			$this->setToken(null);
		}

		return $allCarts;
	}

	/**
	 * @return array|mixed
	 * @throws Mage_Core_Exception
	 */
	function _getNewQuotes()
	{
		$mailchimpStoreId = $this->getMailchimpStoreId();
		$magentoStoreId = $this->getMagentoStoreId();

		$helper = $this->getHelper();
		$dateHelper = $this->getDateHelper();
		$batchId = $this->getBatchId();
		/** @var QC $newCarts */
		$newCarts = $this->buildEcommerceCollectionToSync(Ebizmarts_MailChimp_Model_Config::IS_QUOTE);

		$allCarts = array();

		foreach ($newCarts as $cart) { /** @var Q $cart */
			$cartId = $cart->getEntityId();
			$orderCollection = $this->getOrderCollection();
			$cartCustomerEmail = $cart->getCustomerEmail();
			$orderCollection->addFieldToFilter(
				'main_table.customer_email', array('eq' => $cartCustomerEmail)
			);
			$orderCollection->addFieldToFilter('main_table.updated_at', array('from' => $cart->getUpdatedAt()));
			//if cart is empty or customer has an order made after the abandonment skip current cart.
			$allVisibleItems = $cart->getAllVisibleItems();

			if (empty($allVisibleItems) || $orderCollection->getSize()) {
				$this->addSyncData($cartId);
				continue;
			}

			$customer = $this->getCustomerModel();
			$customer->setWebsiteId($this->getWebSiteIdFromMagentoStoreId($magentoStoreId));
			$customer->loadByEmail($cartCustomerEmail);
			$customerEmail = $customer->getEmail();

			if ($customerEmail != $cartCustomerEmail) {
				$allCartsForEmail = $this->getAllCartsByEmail($cartCustomerEmail);

				foreach ($allCartsForEmail as $cartForEmail) {
					$counter = $this->getCounter();
					$alreadySentCartId = $cartForEmail->getEntityId();
					$allCarts[$counter]['method'] = 'DELETE';
					$allCarts[$counter]['path'] = '/ecommerce/stores/'
						. $mailchimpStoreId
						. '/carts/'
						. $alreadySentCartId;
					$allCarts[$counter]['operation_id'] = $batchId . '_' . $alreadySentCartId;
					$allCarts[$counter]['body'] = '';

					$this->markSyncDataAsDeleted($alreadySentCartId);
					$this->setCounter($counter + 1);
				}

				$allCartsForEmail->clear();
			}

			// don't send the carts for guest customers who are registered
			if (!$cart->getCustomerId() && $customerEmail == $cartCustomerEmail) {
				$this->addSyncData($cartId);
				continue;
			}

			// send the products that not already sent
			$allCarts = $this->addProductNotSentData($cart, $allCarts);
			$cartJson = $this->makeCart($cart, false);

			if ($cartJson !== false) {
				if (!empty($cartJson)) {
					$helper->modifyCounterSentPerBatch(Ebizmarts_MailChimp_Helper_Data::QUO_NEW);

					$counter = $this->getCounter();
					$allCarts[$counter]['method'] = 'POST';
					$allCarts[$counter]['path'] = '/ecommerce/stores/' . $mailchimpStoreId . '/carts';
					$allCarts[$counter]['operation_id'] = $batchId . '_' . $cartId;
					$allCarts[$counter]['body'] = $cartJson;
					$this->setCounter($this->getCounter() + 1);

					$this->addSyncDataToken($cartId, $this->getToken());
				} else {
					# 2024-03-22 Dmitrii Fediuk https://upwork.com/fl/mage2pro
					# "[`Ebizmarts_MailChimp`] The phrase
					# «There is not supported products in this cart» is grammatically incorrect":
					# https://github.com/thehcginstitute-com/m1/issues/522
					$error = $helper->__('There are not supported products in this cart.');

					$this->addSyncDataError(
						$cartId,
						$error,
						null,
						false,
						$dateHelper->getCurrentDateTime()
					);
				}
			} else {
				$jsonErrorMessage = json_last_error_msg();

				$this->addSyncDataError(
					$cartId,
					$jsonErrorMessage,
					null,
					false,
					$dateHelper->getCurrentDateTime()
				);

				//json encode failed
				$this->logSyncError(
					$jsonErrorMessage,
					Ebizmarts_MailChimp_Model_Config::IS_QUOTE,
					$magentoStoreId,
					'magento_side_error',
					'Json Encode Failure',
					0,
					$cart->getId(),
					0
				);
			}

			$this->setToken(null);
		}

		return $allCarts;
	}

	/**
	 * Get all existing carts in the current store view for a given email address.
	 *
	 * @param  $email
	 * @return object
	 */
	function getAllCartsByEmail($email)
	{
		$allCartsForEmail = $this->getItemResourceModelCollection();
		$allCartsForEmail->addFieldToFilter('is_active', array('eq' => 1));
		$allCartsForEmail->addFieldToFilter('store_id', array('eq' => $this->getMagentoStoreId()));
		$allCartsForEmail->addFieldToFilter('customer_email', array('eq' => $email));
		$this->joinLeftEcommerceSyncData($allCartsForEmail);
		// be sure that the quotes are already in mailchimp and not deleted
		$where = "m4m.mailchimp_sync_deleted = 0 "
			. "AND m4m.mailchimp_store_id = '"
			. $this->getMailchimpStoreId() . "'";
		$this->getEcommerceResourceCollection()->addWhere($allCartsForEmail, $where);

		return $allCartsForEmail;
	}

	/**
	 * @used-by self::_getModifiedQuotes()
	 * @used-by self::_getNewQuotes()
	 * @return string|false
	 */
	private function makeCart(Q $q, bool $isModified) {
		$ra = [
			'checkout_url' => $this->_getCheckoutUrl($q, $isModified)
			,'currency_code' => $q->getQuoteCurrencyCode()
			,'customer' => $this->_getCustomer($q, $sid = $this->getMagentoStoreId())
			,'id' => $q->getEntityId()
			,'order_total' => $q->getGrandTotal()
			,'tax_total' => 0
		]; /** @var array string => string|float $ra */
		if ($campaignId = $q['mailchimp_campaign_id']) {
			$ra['campaign_id'] = $campaignId;
		}
		$api = self::apiProducts(); /** @var ApiProducts $api */
		$api->setMagentoStoreId($sid);
		$res = $this->_processCartLines($q->getAllVisibleItems(), $api);
		return !$res['count'] ? '' : json_encode(['lines' => $res['lines']] + $ra);
	}

	/**
	 * @used-by self::makeCart()
	 * @param QI[] $ii
	 * @return array(string => int|array(string => mixed))
	 */
	private function _processCartLines(array $ii, ApiProducts $api):array {
		$lines = [];
		$count = 0;
		foreach ($ii as $i) { /** @var QI $i */
			$pid = (int)$i->getProductId();
			$isTypeProduct = $this->isTypeProduct();
			if ($i->getProductType() == 'bundle' || $i->getProductType() == 'grouped') {
				continue;
			}
			if ($this->isProductTypeConfigurable($i)) {
				$variant = null;
				if ($i->getOptionByCode('simple_product')) {
					$variant = $i->getOptionByCode('simple_product')->getProduct();
				}
				if (!$variant) {
					continue;
				}
				$variantId = $variant->getId();
			}
			else {
				$variantId = $i->getProductId();
			}
			$sd = hcg_mc_syncd_get($pid, $isTypeProduct, $this->getMailchimpStoreId()); /** @var SyncD $sd */
			if (($disabled = !$api->isProductEnabled($pid)) || $sd->time() && !$sd['mailchimp_sync_error']) {
				$lines[] = [
					'id' => (string)++$count // id can not be 0 so we add 1 to $itemCount before setting the id
					,'price' => $i->getRowTotal()
					,'product_id' => $pid
					,'product_variant_id' => $variantId
					,'quantity' => (int)$i->getQty()
				];
				if ($disabled) {
					// update disabled products to remove the product from mailchimp after sending the order
					$api->updateDisabledProducts($pid);
				}
			}
		}
		return ['lines' => $lines, 'count' => $count];
	}

	/**
	 * Get URL for the cart.
	 *
	 * @param  $cart
	 * @param  $isModified
	 * @return string
	 */
	protected function _getCheckoutUrl($cart, $isModified)
	{
		if (!$isModified) {
			$token = hash('md5', rand(0, 9999999));
		} else {
			$token = $cart->getMailchimpToken();
		}

		$url = Mage::getModel('core/url')->setStore($cart->getStoreId())->getUrl(
				'',
				array('_nosid' => true, '_secure' => true)
			)
			. 'mailchimp/cart/loadquote?id=' . $cart->getEntityId() . '&token=' . $token;
		$this->setToken($token);
		return $url;
	}

	/**
	 * @return int
	 */
	protected function getBatchLimitFromConfig()
	{
		$helper = $this->getHelper();
		return $helper->getCartAmountLimit();
	}

	/**
	 * @used-by self::makeCart()
	 * @param  $magentoStoreId
	 */
	private function _getCustomer(Q $cart, $magentoStoreId):array {
		$customer = [
			"id" => hash('md5', strtolower($cart->getCustomerEmail())),
			"email_address" => $cart->getCustomerEmail(),
			"opt_in_status" => $this->getApiCustomersOptIn($magentoStoreId)
		];
		$firstName = $cart->getCustomerFirstname();
		if ($firstName) {
			$customer["first_name"] = $firstName;
		}
		$lastName = $cart->getCustomerLastname();
		if ($lastName) {
			$customer["last_name"] = $lastName;
		}
		$billingAddress = $cart->getBillingAddress();
		if ($billingAddress) {
			$street = $billingAddress->getStreet();
			$address = [];
			if (isset($street[0])) {
				$address['address1'] = $street[0];
				if (count($street) > 1) {
					$address['address2'] = $street[1];
				}
			}
			$address = $this->_addBillingAddress($address, $billingAddress);
			if (!empty($address)) {
				$customer['address'] = $address;
			}
		}
		//company
		if ($billingAddress->getCompany()) {
			$customer["company"] = $billingAddress->getCompany();
		}
		return $customer;
	}

	/**
	 * @param $address
	 * @param $billingAddress
	 * @return array
	 */
	protected function _addBillingAddress($address, $billingAddress)
	{
		if ($billingAddress->getCity()) {
			$address['city'] = $billingAddress->getCity();
		}

		if ($billingAddress->getRegion()) {
			$address['province'] = $billingAddress->getRegion();
		}

		if ($billingAddress->getRegionCode()) {
			$address['province_code'] = $billingAddress->getRegionCode();
		}

		if ($billingAddress->getPostcode()) {
			$address['postal_code'] = $billingAddress->getPostcode();
		}

		if ($billingAddress->getCountry()) {
			$address['country'] = $this->getCountryModel($billingAddress);
			$address['country_code'] = $billingAddress->getCountry();
		}

		return $address;
	}

	/**
	 * @param $cart
	 * @param $allCarts
	 * @return mixed
	 */
	function addProductNotSentData($cart, $allCarts)
	{
		$mailchimpStoreId = $this->getMailchimpStoreId();
		$magentoStoreId = $this->getMagentoStoreId();

		$helper = $this->getHelper();
		$apiProducts = self::apiProducts(); /** @var ApiProducts $apiProducts */
		$apiProducts->setMailchimpStoreId($mailchimpStoreId);
		$apiProducts->setMagentoStoreId($magentoStoreId);

		$productData = $apiProducts->sendModifiedProduct($cart);
		$productDataArray = $helper->addEntriesToArray($allCarts, $productData, $this->getCounter());
		$allCarts = $productDataArray[0];
		$this->setCounter($productDataArray[1]);

		return $allCarts;
	}

	/**
	 * @param Mage_Sales_Model_Resource_Quote_Collection $preFilteredCollection
	 */
	function joinLeftEcommerceSyncData($preFilteredCollection)
	{
		$this->_ecommerceQuotesCollection->joinLeftEcommerceSyncData($preFilteredCollection);
	}

	/**
	 * @return Mage_Sales_Model_Resource_Quote_Collection
	 */
	function getItemResourceModelCollection()
	{
		return Mage::getResourceModel('sales/quote_collection');
	}

	/**
	 * @return false|Mage_Core_Model_Abstract
	 */
	function getCustomerModel()
	{
		return Mage::getModel("customer/customer");
	}

	/**
	 * @param $magentoStoreId
	 * @return mixed
	 */
	function getWebSiteIdFromMagentoStoreId($magentoStoreId)
	{
		return Mage::getModel('core/store')->load($magentoStoreId)->getWebsiteId();
	}

	/**
	 * @return int
	 */
	function getCounter()
	{
		return $this->_counter;
	}

	/**
	 * @param $counter
	 */
	function setCounter($counter)
	{
		$this->_counter = $counter;
	}

	/**
	 * Return the batchId for the batchJson of the carts.
	 *
	 * @return string
	 */
	function getBatchId()
	{
		return $this->_batchId;
	}

	/**
	 * @param $batchId
	 */
	function setBatchId($batchId)
	{
		$this->_batchId = $batchId;
	}

	/**
	 * Token for cart validation.
	 *
	 * @return string|null
	 */
	function getToken()
	{
		return $this->_token;
	}

	/**
	 * @param string $token
	 */
	function setToken($token)
	{

		$this->_token = $token;
	}

	/**
	 * Returns first date of abandoned cart if exists.
	 *
	 * @return string|null
	 */
	protected function getFirstDate()
	{
		return $this->_firstDate;
	}

	/**
	 * @return Mage_Sales_Model_Resource_Order_Collection
	 */
	protected function getOrderCollection()
	{
		return Mage::getResourceModel('sales/order_collection');
	}

	/**
	 * @param $magentoStoreId
	 * @return mixed
	 */
	protected function getApiCustomersOptIn($magentoStoreId)
	{
		return Mage::getModel('mailchimp/api_customers')->getOptIn($magentoStoreId);
	}

	/**
	 * @param $billingAddress
	 * @return mixed
	 */
	protected function getCountryModel($billingAddress)
	{
		return Mage::getModel('directory/country')->loadByCode($billingAddress->getCountry())->getName();
	}

	/**
	 * @param $item
	 * @return bool
	 */
	protected function isProductTypeConfigurable($item)
	{
		return $item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;
	}

	/**
	 * @return string
	 */
	protected function isTypeProduct() {return Ebizmarts_MailChimp_Model_Config::IS_PRODUCT;}

	/**
	 * @return string
	 */
	protected function getItemType()
	{
		return Ebizmarts_MailChimp_Model_Config::IS_QUOTE;
	}

	/**
	 * @return Ebizmarts_MailChimp_Model_Resource_Ecommercesyncdata_Quote_Collection
	 */
	function initializeEcommerceResourceCollection()
	{
		/**
		 * @var $collection Ebizmarts_MailChimp_Model_Resource_Ecommercesyncdata_Quote_Collection
		 */
		$collection = Mage::getResourceModel('mailchimp/ecommercesyncdata_quote_collection');

		return $collection;
	}

	/**
	 * @return Ebizmarts_MailChimp_Model_Resource_Ecommercesyncdata_Quote_Collection
	 */
	function getEcommerceResourceCollection()
	{
		return $this->_ecommerceQuotesCollection;
	}

	/**
	 * @param Mage_Sales_Model_Resource_Quote_Collection $collectionToSync
	 * @param string $isNewItem
	 */
	protected function addFilters(
		Mage_Sales_Model_Resource_Quote_Collection $collectionToSync,
		$isNewItem = "new"
	){
		$magentoStoreId = $this->getMagentoStoreId();
		$collectionToSync->addFieldToFilter('store_id', array('eq' => $magentoStoreId));

		if ($isNewItem == "new") {
			$collectionToSync->addFieldToFilter('is_active', array('eq' => 1));
			$collectionToSync->addFieldToFilter('customer_email', array('notnull' => true));
			$collectionToSync->addFieldToFilter('items_count', array('gt' => 0));

			$this->getHelper()->addResendFilter(
				$collectionToSync, $magentoStoreId, Ebizmarts_MailChimp_Model_Config::IS_QUOTE
			);

			if ($this->getFirstDate()) {
				$collectionToSync->addFieldToFilter('updated_at', array('gt' => $this->getFirstDate()));
			}
		} elseif ($isNewItem == "modified") {
			$collectionToSync->addFieldToFilter('is_active', array('eq' => 1));
		} elseif ($isNewItem == "converted") {
			$collectionToSync->addFieldToFilter('is_active', array('eq' => 0));
		}
	}

	/**
	 * @used-by self::makeCart()
	 * @used-by self::addProductNotSentData()
	 */
	private static function apiProducts():ApiProducts {return Mage::getModel('mailchimp/api_products');}
}
