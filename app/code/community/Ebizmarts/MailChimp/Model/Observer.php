<?php
# 2024-04-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `Ebizmarts_MailChimp_Model_Observer`": https://github.com/thehcginstitute-com/m1/issues/580
use Varien_Event_Observer as Ob;
class Ebizmarts_MailChimp_Model_Observer {

	const PRODUCT_IS_ENABLED = 1;

	/**
	 * @return Mage_Core_Model_Resource
	 */
	protected function getCoreResource()
	{
		return Mage::getSingleton('core/resource');
	}

	/**
	 * @return Mage_Core_Model_Config
	 */
	protected function getConfig()
	{
		return Mage::getConfig();
	}

	/**
	 * @return Ebizmarts_MailChimp_Model_Api_Orders
	 */
	protected function makeApiOrder()
	{
		return Mage::getModel('mailchimp/api_orders');
	}

	/**
	 * @return Ebizmarts_MailChimp_Model_Api_PromoCodes
	 */
	protected function makeApiPromoCode()
	{
		return Mage::getModel('mailchimp/api_promoCodes');
	}

	/**
	 * @return Ebizmarts_MailChimp_Model_Api_PromoRules
	 */
	protected function makeApiPromoRule()
	{
		return Mage::getModel('mailchimp/api_promoRules');
	}

	/**
	 * @return Ebizmarts_MailChimp_Helper_Data
	 */
	protected function makeHelper() {return hcg_mc_h();}

	/**
	 * @return Ebizmarts_MailChimp_Model_Api_Products
	 */
	protected function makeApiProduct()
	{
		return Mage::getModel('mailchimp/api_products');
	}

	/**
	 * @return Ebizmarts_MailChimp_Model_Api_Subscribers
	 */
	protected function makeApiSubscriber()
	{
		return Mage::getModel('mailchimp/api_subscribers');
	}

	/**
	 * @return Ebizmarts_MailChimp_Model_Api_Customers
	 */
	protected function makeApiCustomer()
	{
		return Mage::getModel('mailchimp/api_customers');
	}

	/**
	 * @return Mage_Customer_Model_Customer
	 */
	protected function getCustomerModel()
	{
		return Mage::getModel('customer/customer');
	}

	/**
	 * Handle save of System -> Configuration, section <mailchimp>
	 * @return  Varien_Event_Observer
	 * @throws  Mage_Core_Exception
	 */
	function saveConfigBefore(Ob $ob)
	{
		$config = $ob->getObject();

		if ($config->getSection() == "mailchimp") {
			$configData = $config->getData();
			$configDataChanged = false;
			$helper = $this->makeHelper();
			$scopeArray = $helper->getCurrentScope();
			$mailchimpStoreId = (isset($configData['groups']['general']['fields']['storeid']['value']))
				? $configData['groups']['general']['fields']['storeid']['value']
				: null;
			$mcStoreOld = hcg_mc_sid($scopeArray['scope_id']); /** @var ?string $mcStoreOld */

			if (isset($configData['groups']['general']['fields']['apikey']['value'])
				&& !$helper->isApiKeyObscure($configData['groups']['general']['fields']['apikey']['value'])
			) {
				$apiKey = $configData['groups']['general']['fields']['apikey']['value'];
			} else {
				$helper->getApiKey($scopeArray['scope_id'], $scopeArray['scope']);
			}

			// If ecommerce data section is enabled only allow inheriting both entries
			// (list and MC Store) at the same time.

			if ($this->isListXorStoreInherited($configData)) {
				if (isset($configData['groups']['general']['fields']['list']['inherit'])) {
					unset($configData['groups']['general']['fields']['list']['inherit']);
					$mcStoreListId = $helper->getListIdByApiKeyAndMCStoreId($apiKey, $mailchimpStoreId);
					$previouslyConfiguredListId = $helper->getGeneralList(
						$scopeArray['scope_id'],
						$scopeArray['scope']
					);
					$listId = (!empty($previouslyConfiguredListId)) ? $previouslyConfiguredListId : $mcStoreListId;
					$configData['groups']['general']['fields']['list']['value'] = $listId;
					$configDataChanged = true;
					$message = $helper->__(
						'The audience configuration was automatically modified to show the audience '
						. 'associated to the selected Mailchimp store.'
					);
					$this->getAdminSession()->addError($message);
				} elseif (isset($configData['groups']['general']['fields']['storeid']['inherit'])) {
					unset($configData['groups']['general']['fields']['storeid']['inherit']);
					$configData['groups']['general']['fields']['storeid']['value'] = $mcStoreOld;
					$configDataChanged = true;
					$message = $helper->__(
						'The Mailchimp store configuration was not modified. There is a Mailchimp audience configured '
						. 'for this scope. Both must be set to inherit at the same time.'
					);
					$this->getAdminSession()->addError($message);
				}

				if ($configDataChanged) {
					$config->setData($configData);
				}
			}
		}

		return $ob;
	}

	/**
	 * return true if list or store is selected but the other one is inheriting.
	 *
	 * @param  $configData
	 * @return bool
	 */
	protected function isListXorStoreInherited($configData)
	{
		return (
			!isset($configData['groups']['general']['fields']['list']['inherit'])
			&& isset($configData['groups']['general']['fields']['storeid']['value'])
			|| !isset($configData['groups']['general']['fields']['storeid']['inherit'])
			&& isset($configData['groups']['general']['fields']['list']['value']));
	}

	/**
	 * Handle confirmation emails and subscription to Mailchimp
	 *
	 * @param   Varien_Event_Observer $observer
	 * @return  Varien_Event_Observer
	 * @throws  Mage_Core_Exception
	 */
	function subscriberSaveBefore(Varien_Event_Observer $observer)
	{
		$subscriber = $observer->getEvent()->getSubscriber();
		$subscriberSource = $subscriber->getSubscriberSource();
		$storeId = $subscriber->getStoreId();
		$helper = $this->makeHelper();
		$isEnabled = $helper->isSubscriptionEnabled($storeId);

		if ($isEnabled && !$this->isMailchimpSave($subscriberSource)) {
			$statusChanged = $subscriber->getIsStatusChanged();

			//Override Magento status to always send double opt-in confirmation.
			if ($statusChanged && $subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED
				&& $helper->isSubscriptionConfirmationEnabled($storeId) && !$helper->isUseMagentoEmailsEnabled($storeId)
			) {
				$subscriber->setStatus(Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE);
				$this->addSuccessIfRequired($helper);
			}
		}

		return $observer;
	}

	/**
	 * Handle interest groups for subscriber and allow Magento email to be sent if configured that way.
	 *
	 * @param   Varien_Event_Observer $observer
	 * @return  Varien_Event_Observer
	 * @throws  Mage_Core_Exception
	 * @throws  Mage_Core_Model_Store_Exception
	 */
	function subscriberSaveAfter(Varien_Event_Observer $observer)
	{
		$subscriber = $observer->getEvent()->getSubscriber();
		# 2024-04-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Delete the `->getMailchimpStoreView()` / `mailchimp_store_view` calls for `Mage_Customer_Model_Customer`
		# because it always returns `NULL`": https://github.com/thehcginstitute-com/m1/issues/578
		$storeViewId = (int)$subscriber->getStoreId(); /** @var int $storeViewId */
		$helper = $this->makeHelper();
		$isEnabled = $helper->isSubscriptionEnabled($storeViewId);
		$subscriberSource = $subscriber->getSubscriberSource();

		if ($isEnabled && !$this->isMailchimpSave($subscriberSource)) {
			$params = $this->getRequest()->getParams();
			$helper->saveInterestGroupData($params, $storeViewId, null, $subscriber);

			$this->createEmailCookie($subscriber);

			$apiSubscriber = $this->makeApiSubscriber();

			if ($helper->isUseMagentoEmailsEnabled($storeViewId) !== 1
				&& $this->isMagentoSubscription($subscriberSource)
				|| $this->isEmailConfirmationRequired($subscriberSource)
			) {
				if ($subscriber->getIsStatusChanged()) {
					$apiSubscriber->updateSubscriber($subscriber, true);
				} else {
					$origData = $subscriber->getOrigData();

					if (is_array($origData) && isset($origData['subscriber_status'])
						&& $origData['subscriber_status'] != $subscriber->getSubscriberStatus()
					) {
						$apiSubscriber->updateSubscriber($subscriber, true);
					}
				}
			} else {
				$subscriber->setImportMode(false);
			}
		}

		return $observer;
	}

	/**
	 * Handle subscriber deletion from back end.
	 *
	 * @param  Varien_Event_Observer $observer
	 * @return Varien_Event_Observer
	 */
	function handleSubscriberDeletion(Varien_Event_Observer $observer)
	{
		$subscriber = $observer->getEvent()->getSubscriber();
		$helper = $this->makeHelper();
		$isEnabled = $helper->isSubscriptionEnabled($subscriber->getStoreId());

		if ($isEnabled) {
			$this->makeApiSubscriber()->deleteSubscriber($subscriber);
		}

		return $observer;
	}

	/**
	 * Add Subscriber first name and last name to Newsletter Grid.
	 *
	 * @param  Varien_Event_Observer $observer
	 * @return $this|Varien_Event_Observer
	 */
	function alterNewsletterGrid(Varien_Event_Observer $observer)
	{

		$block = $observer->getEvent()->getBlock();
		if (!isset($block)) {
			return $this;
		}

		if ($block instanceof Mage_Adminhtml_Block_Newsletter_Subscriber_Grid) {
			$block->addColumnAfter(
				'firstname',
				array(
					'header' => Mage::helper('newsletter')->__('Customer First Name'),
					'index' => 'customer_firstname',
					'renderer' => 'mailchimp/adminhtml_newsletter_subscriber_renderer_firstname',
				),
				'type'
			);

			$block->addColumnAfter(
				'lastname',
				array(
					'header' => Mage::helper('newsletter')->__('Customer Last Name'),
					'index' => 'customer_lastname',
					'renderer' => 'mailchimp/adminhtml_newsletter_subscriber_renderer_lastname'
				),
				'firstname'
			);
		}

		return $observer;
	}

	/**
	 * When Customer object is saved set it to be updated on MailChimp if getMailchimpUpdateObserverRan() is false.
	 *
	 * @param  Varien_Event_Observer $observer
	 * @return Varien_Event_Observer
	 */
	function customerSaveAfter(Varien_Event_Observer $observer)
	{
		$customer = $observer->getEvent()->getCustomer();
		$origEmail = $customer->getOrigData('email');
		$customerEmail = $customer->getEmail();
		# 2024-04-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# `$customer->getStoreId()` returns a string like «1».
		$storeId = (int)$customer->getStoreId(); /** @var int $storeId */
		# 2024-04-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# 1) "«Argument 1 passed to Ebizmarts_MailChimp_Helper_Data::isEcomSyncDataEnabled()
		# must be of the type int, null given»": https://github.com/thehcginstitute-com/m1/issues/577
		# 2) "Delete the `->getMailchimpStoreView()` / `mailchimp_store_view` calls for `Mage_Customer_Model_Customer`
		# because it always returns `NULL`": https://github.com/thehcginstitute-com/m1/issues/578
		$helper = $this->makeHelper();
		$isEnabled = $helper->isSubscriptionEnabled($storeId);
		$params = $this->getRequest()->getParams();

		if ($isEnabled) {
			$customerId = $customer->getId();
			$subscriberEmail = ($origEmail) ? $origEmail : $customerEmail;
			$subscriber = $this->handleCustomerGroups($subscriberEmail, $params, $storeId, $customerId);
			$apiSubscriber = $this->makeApiSubscriber();

			if ($origEmail) {
				// check if customer has changed email address
				if ($origEmail != $customerEmail) {
					if ($subscriber->getId()) {
						// unsubscribe old email address
						$apiSubscriber->deleteSubscriber($subscriber);

						// subscribe new email address
						$subscriberModel = df_subscriber();
						$subscriber = $subscriberModel->loadByCustomer($customer);
						$subscriber->setSubscriberEmail($customerEmail); // make sure we set the new email address
						$subscriber->save();
					}
				}
			}

			//update subscriber data if a subscriber with the same email address exists and was not affected.
			if (!$origEmail || $origEmail == $customerEmail) {
				$apiSubscriber->update($customerEmail);
			}

			if ($helper->isEcomSyncDataEnabled($storeId)) {
				//update mailchimp ecommerce data for that customer
				$apiCustomer = $this->makeApiCustomer();
				$apiCustomer->setMailchimpStoreId(hcg_mc_sid($storeId));
				$apiCustomer->setMagentoStoreId($storeId);
				$apiCustomer->update($customerId);
			}
		}

		return $observer;
	}

	function customerAddressSaveBefore(Varien_Event_Observer $observer) {
		$customerId = $observer->getEvent()->getCustomerAddress()->getCustomerId();
		$customer = $this->getCustomerModel()->load($customerId);
		$storeId = $customer->getStoreId(); /** @var int $storeId */
		$helper = $this->makeHelper();
		if ($helper->isSubscriptionEnabled($storeId)) {
			//update subscriber data if a subscriber with the same email address exists
			$this->makeApiSubscriber()->update($customer->getEmail());
		}
		if ($helper->isEcomSyncDataEnabled($storeId)) {
			//update mailchimp ecommerce data for that customer
			$apiCustomer = $this->makeApiCustomer();
			$apiCustomer->setMailchimpStoreId(hcg_mc_sid($storeId));
			$apiCustomer->setMagentoStoreId($storeId);
			$apiCustomer->update($customerId);
		}

		return $observer;
	}

	/**
	 * Set the products included the order to be updated on MailChimp on the next cron job run.
	 *
	 * @param  Varien_Event_Observer $observer
	 * @return Varien_Event_Observer
	 * @throws Exception
	 */
	function newOrder(Varien_Event_Observer $observer)
	{
		$helper = $this->makeHelper();
		$post = $helper->getMageApp()->getRequest()->getPost('mailchimp_subscribe');
		$order = $observer->getEvent()->getOrder();
		$storeId = $order->getStoreId(); /** @var int $storeId */
		$ecommEnabled = $helper->isEcomSyncDataEnabled($storeId);
		$subEnabled = $helper->isSubscriptionEnabled($storeId);
		if ($subEnabled) {
			if (isset($post)) {
				$email = $order->getCustomerEmail();
				$subscriber = hcg_mc_sub($post, $email);

				if ($subscriber) {
					if (!$subscriber->getCustomerId()) {
						$subscriber->setSubscriberFirstname($order->getCustomerFirstname());
						$subscriber->setSubscriberLastname($order->getCustomerLastname());
					}

					$subscriber->subscribe($email);
				}
			}
		}

		if ($ecommEnabled) {
			$this->removeCampaignData();
			$items = $order->getAllItems();
			try {
				foreach ($items as $item) {
					if ($this->isBundleItem($item) || $this->isConfigurableItem($item)) {
						continue;
					}
					$mcStore = hcg_mc_sid($storeId); /** @var string $mcStore */
					$productId = (int)$item->getProductId();
					$dataProduct = hcg_mc_syncd_get(
						$productId,
						Ebizmarts_MailChimp_Model_Config::IS_PRODUCT,
						$mcStore
					);
					$isMarkedAsDeleted = $dataProduct->getMailchimpSyncDeleted();
					$isMarkedAsModified = $dataProduct->getMailchimpSyncModified();

					if (!$isMarkedAsDeleted && !$isMarkedAsModified) {
						$apiProducts = $this->makeApiProduct();
						$apiProducts->setMailchimpStoreId($mcStore);
						$apiProducts->setMagentoStoreId($storeId);
						$apiProducts->update($productId);
					}
				}
			}
			catch (Exception $e) {df_log($e);}
		}
		return $observer;
	}

	/**
	 * Catch order save before event, mark it as modified and associate the landing page to the order data.
	 *
	 * @param  Varien_Event_Observer $observer
	 * @return Varien_Event_Observer
	 */
	function orderSaveBefore(Varien_Event_Observer $observer) {
		$order = $observer->getEvent()->getOrder();
		$storeId = $order->getStoreId(); /** @var int $storeId */
		$apiOrder = $this->makeApiOrder();
		$helper = $this->makeHelper();
		$ecommEnabled = $helper->isEcomSyncDataEnabled($storeId);
		if ($ecommEnabled) {
			$apiOrder->setMailchimpStoreId(hcg_mc_sid($storeId));
			$apiOrder->update($order->getId(), $storeId);
		}
		return $observer;
	}

	/**
	 * When Order object is saved add the campaign id if available in the cookies.
	 *
	 * @param  Varien_Event_Observer $observer
	 * @return Varien_Event_Observer
	 */
	function saveCampaignData(Varien_Event_Observer $observer)
	{
		$order = $observer->getEvent()->getOrder();
		$campaignCookie = $this->_getCampaignCookie();

		if ($campaignCookie) {
			$order->setMailchimpCampaignId($campaignCookie);
		}

		$landingCookie = $this->_getLandingCookie();

		if ($landingCookie && !$order->getMailchimpLandingPage()) {
			$order->setMailchimpLandingPage($landingCookie);
		}

		return $observer;
	}

	/**
	 * Delete campaign and landing cookies.
	 *
	 * @return Varien_Event_Observer
	 * @throws Exception
	 */
	function removeCampaignData()
	{
		if ($this->_getCampaignCookie()) {
			Mage::getModel('core/cookie')->delete('mailchimp_campaign_id');
		}

		if ($this->_getLandingCookie()) {
			Mage::getModel('core/cookie')->delete('mailchimp_landing_page');
		}
	}

	/**
	 * Get campaign cookie if available.
	 *
	 * @return mixed
	 */
	protected function _getCampaignCookie()
	{
		return Mage::getModel('core/cookie')->get('mailchimp_campaign_id');
	}

	/**
	 * Get landing_page cookie if exists.
	 *
	 * @return null
	 */
	protected function _getLandingCookie()
	{
		return Mage::getModel('core/cookie')->get('mailchimp_landing_page');
	}

	/**
	 * Add column to associate orders in grid gained from MailChimp campaigns and automations.
	 *
	 * @param  Varien_Event_Observer $observer
	 * @return Varien_Event_Observer
	 * @throws Mage_Core_Exception
	 */
	function addColumnToSalesOrderGrid(Varien_Event_Observer $observer)
	{
		$helper = $this->makeHelper();
		$addColumnConfig = $helper->getMonkeyInGrid(0);
		$ecommEnabledAnyScope = $helper->isEcomSyncDataEnabledInAnyScope();
		$block = $observer->getEvent()->getBlock();
		if ($block instanceof Mage_Adminhtml_Block_Sales_Order_Grid
			&& $ecommEnabledAnyScope && $addColumnConfig
		) {
			if ($addColumnConfig == Ebizmarts_MailChimp_Model_Config::ADD_MAILCHIMP_LOGO_TO_GRID
				|| $addColumnConfig == Ebizmarts_MailChimp_Model_Config::ADD_BOTH_TO_GRID
			) {
				$block->addColumnAfter(
					'mailchimp_campaign_id',
					array(
						'header' => $helper->__('MailChimp'),
						'index' => 'mailchimp_campaign_id',
						'align' => 'center',
						'filter' => false,
						'renderer' => 'mailchimp/adminhtml_sales_order_grid_renderer_mailchimp',
						'sortable' => false,
						'width' => 70
					),
					'created_at'
				);
			}

			if ($addColumnConfig == Ebizmarts_MailChimp_Model_Config::ADD_SYNC_STATUS_TO_GRID
				|| $addColumnConfig == Ebizmarts_MailChimp_Model_Config::ADD_BOTH_TO_GRID
			) {
				$block->addColumnAfter(
					'mailchimp_synced_flag',
					array(
						'header' => $helper->__('Synced to MailChimp'),
						'index' => 'mailchimp_synced_flag',
						'align' => 'center',
						'filter' => false,
						'renderer' => 'mailchimp/adminhtml_sales_order_grid_renderer_mailchimpOrder',
						'sortable' => true,
						'width' => 70
					),
					'created_at'
				);

				$columnId = $block->getParam($block->getVarNameSort());
				$direction = $block->getParam($block->getVarNameDir());
				if ($columnId == 'mailchimp_synced_flag') {
					Mage::register('sort_column_dir', $direction);
				}
			}
		}

		return $observer;
	}


	function addColumnToSalesOrderGridCollection(Varien_Event_Observer $observer)
	{
		$helper = $this->makeHelper();
		$addColumnConfig = $helper->getMonkeyInGrid(0);
		$ecommEnabledAnyScope = $helper->isEcomSyncDataEnabledInAnyScope();

		if ($ecommEnabledAnyScope && $addColumnConfig) {
			$collection = $observer->getOrderGridCollection();
			$select = $collection->getSelect();
			$fromClause = $select->getPart(Zend_Db_Select::FROM);
			//check if mc alias is already defined, avoids possible conflicts
			if (array_key_exists('mc', $fromClause)) {
				return;
			}

			$adapter = $this->getCoreResource()->getConnection('core_write');
			$select->joinLeft(
				array('mc' => $collection->getTable('mailchimp/ecommercesyncdata')),
				$adapter->quoteInto(
					'mc.related_id=main_table.entity_id AND type = ?',
					Ebizmarts_MailChimp_Model_Config::IS_ORDER
				),
				array('mc.mailchimp_synced_flag', 'mc.id')
			);
			$select->group("main_table.entity_id");
			$direction = $this->getRegistry();

			if ($direction) {
				$collection->addOrder('mc.id', $direction);
				$this->removeRegistry();
			}
		}
	}

	/**
	 * Add customer to the cart if it placed the email address in the popup or footer subscription form.
	 *
	 * @param  Varien_Event_Observer $observer
	 * @return Varien_Event_Observer
	 */
	function loadCustomerToQuote(Varien_Event_Observer $observer)
	{
		$quote = $observer->getEvent()->getQuote();
		$storeId = $quote->getStoreId();
		$helper = $this->makeHelper();
		$isEcomEnabled = $helper->isEcomSyncDataEnabled($storeId);
		$isAbandonedCartEnabled = $helper->isAbandonedCartEnabled($storeId);
		$email = null;

		if (!$this->isCustomerLoggedIn()
			&& $isEcomEnabled && $isAbandonedCartEnabled
		) {
			$action = $this->getRequestActionName();
			$onCheckout = ($action == 'saveOrder' || $action == 'savePayment' ||
				$action == 'saveShippingMethod' || $action == 'saveBilling');
			$emailCookie = $this->getEmailCookie();

			if ($emailCookie && $emailCookie != 'none' && !$onCheckout
			) {
				$email = $this->getEmailFromPopUp($emailCookie);
			}

			if ($quote->getCustomerEmail() != $email && $email !== null) {
				$quote->setCustomerEmail($email);
			}
		}

		$campaignId = $this->_getCampaignCookie();
		if ($campaignId) {
			$quote->setMailchimpCampaignId($campaignId);
		}

		$landingCookie = $this->_getLandingCookie();
		if ($landingCookie) {
			$quote->setMailchimpLandingPage($landingCookie);
		}

		return $observer;
	}

	/**
	 * Set the products included in the credit memo to be updated on MailChimp on the next cron job run.
	 *
	 * @param  Varien_Event_Observer $observer
	 * @return Varien_Event_Observer
	 */
	function newCreditMemo(Varien_Event_Observer $observer) {
		$creditMemo = $observer->getEvent()->getCreditmemo();
		$order = $creditMemo->getOrder();
		$storeId = $order->getStoreId(); /** @var int $storeId */
		$helper = $this->makeHelper();
		$ecomEnabled = $helper->isEcomSyncDataEnabled($storeId);
		$apiProduct = $this->makeApiProduct();
		$apiOrder = $this->makeApiOrder();

		if ($ecomEnabled) {
			$mcStore = hcg_mc_sid($storeId); /** @var string $mcStore */
			$apiProduct->setMailchimpStoreId($mcStore);
			$apiProduct->setMagentoStoreId($storeId);
			$items = $creditMemo->getAllItems();
			foreach ($items as $item) {
				if ($this->isBundleItem($item) || $this->isConfigurableItem($item)) {
					continue;
				}
				$productId = (int)$item->getProductId();
				$dataProduct = hcg_mc_syncd_get(
					$productId,
					Ebizmarts_MailChimp_Model_Config::IS_PRODUCT,
					$mcStore
				);
				$isMarkedAsDeleted = $dataProduct->getMailchimpSyncDeleted();

				if (!$isMarkedAsDeleted) {
					$apiProduct->update($productId);
				}
			}
			$apiOrder->update($order->getEntityId(), $storeId);
		}
		return $observer;
	}

	/**
	 * If "unsubscribe" checkbox is checked, ubsubscribes the customer.
	 *
	 * @param  Varien_Event_Observer $observer
	 * @return Varien_Event_Observer
	 */
	function createCreditmemo($observer) {
		$mailchimpUnsubscribe = $this->getRequest()->getParam('mailchimp_unsubscribe');
		if ($this->isUnsubscribeChecked($mailchimpUnsubscribe)) {
			$creditMemo = $observer->getEvent()->getCreditmemo();
			hcg_mc_unsubscribe(df_subscriber($creditMemo->getOrder()->getCustomerEmail()));
		}
		return $observer;
	}

	/**
	 * Set the products included in the credit memo to be updated on MailChimp on the next cron job run.
	 *
	 * @param  Varien_Event_Observer $observer
	 * @return Varien_Event_Observer
	 */
	function cancelCreditMemo(Varien_Event_Observer $observer)
	{
		$creditMemo = $observer->getEvent()->getCreditmemo();
		$order = $creditMemo->getOrder();
		$storeId = $order->getStoreId(); /** @var int $storeId */
		$helper = $this->makeHelper();
		$ecomEnabled = $helper->isEcomSyncDataEnabled($storeId);
		$apiProduct = $this->makeApiProduct();
		$apiOrder = $this->makeApiOrder();
		if ($ecomEnabled) {
			$mcStore = hcg_mc_sid($storeId); /** @var string $mcStore */
			$apiProduct->setMagentoStoreId($storeId);
			$apiProduct->setMailchimpStoreId($mcStore);
			$items = $creditMemo->getAllItems();
			foreach ($items as $item) {
				if ($this->isBundleItem($item) || $this->isConfigurableItem($item)) {
					continue;
				}
				$productId = (int)$item->getProductId();
				$dataProduct = hcg_mc_syncd_get(
					$productId,
					Ebizmarts_MailChimp_Model_Config::IS_PRODUCT,
					$mcStore
				);
				$isMarkedAsDeleted = $dataProduct->getMailchimpSyncDeleted();
				if (!$isMarkedAsDeleted) {
					$apiProduct->update($productId);
				}
			}
			$apiOrder->update($order->getEntityId(), $storeId);
		}
		return $observer;
	}

	/**
	 * Set the products canceled to be updated on MailChimp on the next cron job run.
	 *
	 * @param  Varien_Event_Observer $observer
	 * @return Varien_Event_Observer
	 * @throws Exception
	 */
	function itemCancel(Varien_Event_Observer $observer) {
		$item = $observer->getEvent()->getItem();
		$helper = $this->makeHelper();
		$storeId = $item->getStoreId(); /** @var int $storeId */
		$ecomEnabled = $helper->isEcomSyncDataEnabled($storeId);
		$apiProduct = $this->makeApiProduct();
		if ($ecomEnabled) {
			$mcStore = hcg_mc_sid($storeId); /** @var string $mcStore */
			$apiProduct->setMailchimpStoreId($mcStore);
			$apiProduct->setMagentoStoreId($storeId);
			$productId = (int)$item->getProductId();
			$dataProduct = hcg_mc_syncd_get(
				$productId,
				Ebizmarts_MailChimp_Model_Config::IS_PRODUCT,
				$mcStore
			);
			$isMarkedAsDeleted = $dataProduct->getMailchimpSyncDeleted();
			if (!$this->isBundleItem($item) && !$this->isConfigurableItem($item) && !$isMarkedAsDeleted) {
				$apiProduct->update($productId);
			}
		}
		return $observer;
	}

	/**
	 * When Product object is saved set it to be updated on MailChimp if getMailchimpUpdateObserverRan() is false.
	 *
	 * @param  Varien_Event_Observer $observer
	 * @return Varien_Event_Observer
	 */
	function productSaveAfter(Varien_Event_Observer $observer) {
		$product = $observer->getEvent()->getProduct();
		$helper = $this->makeHelper();
		$apiProduct = $this->makeApiProduct();
		$stores = $helper->getMageApp()->getStores();
		foreach ($stores as $storeId => $store) { /** @var int $storeId */
			$ecommEnabled = $helper->isEcommerceEnabled($storeId);
			if ($ecommEnabled) {
				$mcStore = hcg_mc_sid($storeId); /** @var string $mcStore */
				$apiProduct->setMailchimpStoreId($mcStore);
				$apiProduct->setMagentoStoreId($storeId);
				$status = $this->getCatalogProductStatusModel()->getProductStatus($product->getId(), $storeId);
				if ($status[$product->getId()] == self::PRODUCT_IS_ENABLED) {
					$dataProduct = hcg_mc_syncd_get(
						(int)$product->getId(),
						Ebizmarts_MailChimp_Model_Config::IS_PRODUCT,
						$mcStore
					);
					$isMarkedAsDeleted = $dataProduct->getMailchimpSyncDeleted();
					$errorMessage = $dataProduct['mailchimp_sync_error'];

					if ($isMarkedAsDeleted
						|| $errorMessage == Ebizmarts_MailChimp_Model_Api_Products::PRODUCT_DISABLED_IN_MAGENTO
					) {
						$dataProduct->delete();
					} else {
						$apiProduct->update($product->getId());
					}
				} else {
					$apiProduct->updateDisabledProducts($product->getId());
				}
			}
		}

		return $observer;
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return Varien_Event_Observer
	 */
	function productAttributeUpdate(Varien_Event_Observer $observer)
	{
		$productIds = $observer->getEvent()->getProductIds();
		$helper = $this->makeHelper();
		$apiProduct = $this->makeApiProduct();
		$mailchimpStoreIdsArray = $helper->getAllMailChimpStoreIds();

		foreach ($mailchimpStoreIdsArray as $scopeData => $mailchimpStoreId) {
			$scopeArray = $this->getScopeArrayFromString($scopeData);
			$ecommEnabled = $helper->isEcommerceEnabled($scopeArray['scope_id'], $scopeArray['scope']);
			$apiProduct->setMailchimpStoreId($mailchimpStoreId);

			if ($ecommEnabled) {
				foreach ($productIds as $productId) {
					$dataProduct = hcg_mc_syncd_get(
						(int)$productId,
						Ebizmarts_MailChimp_Model_Config::IS_PRODUCT,
						$mailchimpStoreId
					);

					$isMarkedAsDeleted = $dataProduct->getMailchimpSyncDeleted();

					if (!$isMarkedAsDeleted) {
						$apiProduct->update($productId);
					}
				}
			}
		}

		return $observer;
	}

	/**
	 * @param $emailCookie
	 * @return mixed
	 */
	protected function getEmailFromPopUp($emailCookie)
	{
		$emailCookieArr = explode('/', $emailCookie);
		$email = $emailCookieArr[0];
		$email = str_replace(' ', '+', $email);

		return $email;
	}

	function salesruleSaveAfter(Varien_Event_Observer $observer)
	{
		$promoRulesApi = $this->makeApiPromoRule();
		$rule = $observer->getEvent()->getRule();
		$ruleId = $rule->getRuleId();
		$promoRulesApi->update($ruleId);

		$promoCodesCollection = Mage::getModel('salesrule/coupon')->getCollection()
			->addFieldToFilter('rule_id', $ruleId);

		$promoCodesApi = $this->makeApiPromoCode();
		foreach ($promoCodesCollection as $promoCode) {
			$promoCodesApi->update($promoCode->getId());
		}

		return $observer;
	}

	function salesruleDeleteAfter(Varien_Event_Observer $observer)
	{
		$rule = $observer->getEvent()->getRule();
		$ruleId = $rule->getRuleId();
		$couponId = $rule->getPrimaryCoupon()->getData() ['coupon_id'];

		$promoRulesApi = $this->makeApiPromoRule();
		$promoRulesApi->markAsDeleted($ruleId);

		$promoCodesApi = $this->makeApiPromoCode();
		$promoCodesApi->markAsDeleted($couponId, $ruleId);

		return $observer;
	}

	function secondaryCouponsDelete(Varien_Event_Observer $observer)
	{
		$promoCodesApi = $this->makeApiPromoCode();
		$params = $this->getRequest()->getParams();

		if (isset($params['ids']) && isset($params['id'])) {
			$promoRuleId = $params['id'];
			$promoCodeIds = $params['ids'];

			foreach ($promoCodeIds as $promoCodeId) {
				$promoCodesApi->markAsDeleted($promoCodeId, $promoRuleId);
			}
		}

		return $observer;
	}

	function cleanProductImagesCacheAfter(Varien_Event_Observer $observer)
	{
		$message = 'Image cache has been flushed please resend the products in order to update image URL.';
		$helper = $this->makeHelper();
		hcg_mc_cfg_save(Ebizmarts_MailChimp_Model_Config::PRODUCT_IMAGE_CACHE_FLUSH, 1);
		$helper->addAdminWarning($message);

		return $observer;
	}

	protected function markProductsAsModified()
	{
		$tableName = $mailchimpTableName = $this->getCoreResource()
			->getTableName('mailchimp/ecommercesyncdata');
		$sqlQuery = "UPDATE " . $tableName . " "
			. "SET mailchimp_sync_modified = 1 "
			. "WHERE type = '" . Ebizmarts_MailChimp_Model_Config::IS_PRODUCT . "';";
		$connection = $this->getCoreResource()->getConnection('core_write');
		$connection->query($sqlQuery);
	}

	/**
	 * @param $item
	 * @return bool
	 */
	protected function isBundleItem($item)
	{
		return $item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE;
	}

	/**
	 * @param $item
	 * @return bool
	 */
	protected function isConfigurableItem($item)
	{
		return $item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;
	}

	/**
	 * Transform array in format scope_scopeId to array.
	 *
	 * @param  $scopeData
	 * @return array
	 */
	protected function getScopeArrayFromString($scopeData)
	{
		$scopeArray = explode('_', $scopeData);
		return array('scope' => $scopeArray[0], 'scope_id' => $scopeArray[1]);
	}

	/**
	 * @return string|null
	 */
	protected function getRegistry()
	{
		return Mage::registry('sort_column_dir');
	}

	protected function removeRegistry()
	{
		return Mage::unregister('sort_column_dir');
	}

	/**
	 * Add success message if subscribing from customer account.
	 *
	 * @param $helper
	 */
	protected function addSuccessIfRequired($helper)
	{
		$request = Mage::app()->getRequest();
		$module = $request->getControllerModule();
		$moduleController = $request->getControllerName();
		$moduleControllerAction = $request->getActionName();
		$fullActionName = $module . '_' . $moduleController . '_' . $moduleControllerAction;

		if (strstr($fullActionName, 'Mage_Newsletter_manage_save')) {
			Mage::getSingleton('customer/session')->addSuccess(
				$helper->__('Confirmation request has been sent.')
			);
		}
	}

	/**
	 * @param $subscriber
	 * @throws Mage_Core_Model_Store_Exception
	 */
	protected function createEmailCookie($subscriber)
	{
		if (!$this->isCustomerLoggedIn() && !Mage::app()->getStore()->isAdmin()) {
			Mage::getModel('core/cookie')->set(
				'email',
				$subscriber->getSubscriberEmail(),
				null,
				null,
				null,
				null,
				false
			);
		}
	}

	protected function getRequest()
	{
		return Mage::app()->getRequest();
	}

	/**
	 * Handle frontend customer interest groups only if is not subscribed and all admin customer groups.
	 *
	 * @param       $subscriberEmail
	 * @param       $params
	 * @param       $storeId
	 * @param null  $customerId
	 * @return Mage_Newsletter_Model_Subscriber
	 * @throws Mage_Core_Model_Store_Exception
	 */
	function handleCustomerGroups($subscriberEmail, $params, $storeId, $customerId = null)
	{
		$helper = $this->makeHelper();
		$subscriber = df_subscriber($subscriberEmail);
		if ($subscriber->getId()) {
			$helper->saveInterestGroupData($params, $storeId, $customerId, $subscriber);
		}
		elseif (isset($params['customer_id'])) {
			$groups = $helper->getInterestGroupsIfAvailable($params);

			if ($groups) {
				$helper->saveInterestGroupData($params, $storeId, $customerId);
				$this->getWarningMessageAdminHtmlSession($helper);
			}
		}
		else {
			//save frontend groupdata when customer is not subscribed.
			$helper->saveInterestGroupData($params, $storeId, $customerId);
		}
		return $subscriber;
	}

	/**
	 * @return boolean
	 */
	protected function isUnsubscribeChecked($mailchimpUnsubscribe)
	{
		if ($mailchimpUnsubscribe === 'on') {
			return true;
		}

		return false;
	}

	/**
	 * @return mixed
	 */
	protected function getEmailCookie()
	{
		$emailCookie = Mage::getModel('core/cookie')->get('email');

		return $emailCookie;
	}

	/**
	 * @return mixed
	 */
	protected function isCustomerLoggedIn()
	{
		return Mage::getSingleton('customer/session')->isLoggedIn();
	}

	/**
	 * @return string
	 */
	protected function getRequestActionName()
	{
		return $this->getRequest()->getActionName();
	}

	/**
	 * @param $helper
	 * @return mixed
	 */
	protected function getWarningMessageAdminHtmlSession($helper)
	{
		return Mage::getSingleton('adminhtml/session')->addWarning(
			$helper->__('The customer must be subscribed for this change to apply.')
		);
	}

	/**
	 * @return Mage_Catalog_Model_Product_Status
	 */
	protected function getCatalogProductStatusModel()
	{
		return Mage::getModel('catalog/product_status');
	}

	/**
	 * @param string $subscriberSource
	 * @return bool
	 */
	protected function isEmailConfirmationRequired($subscriberSource)
	{
		return $subscriberSource === Ebizmarts_MailChimp_Model_Subscriber::SUBSCRIBE_CONFIRMATION;
	}

	/**
	 * @param string $subscriberSource
	 * @return bool
	 */
	protected function isMagentoSubscription($subscriberSource)
	{
		return empty($subscriberSource);
	}

	/**
	 * @param string $subscriberSource
	 * @return bool
	 */
	protected function isMailchimpSave($subscriberSource)
	{
		return $subscriberSource === Ebizmarts_MailChimp_Model_Subscriber::MAILCHIMP_SUBSCRIBE;
	}
	function productImportAfter($observer)
	{
		$helper = $this->makeHelper();
		$adapter = $observer->getEvent()->getAdapter();
		$affectedIds = $adapter->getAffectedEntityIds();
		Mage::getModel('mailchimp/api_products');
		$apiProduct = $this->makeApiProduct();
		foreach ($affectedIds as $id) {
			$apiProduct->markAllAsModified($id);
		}
	}
}
