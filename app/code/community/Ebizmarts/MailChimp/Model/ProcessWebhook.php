<?php
# 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `Ebizmarts_MailChimp_Model_ProcessWebhook`": https://github.com/cabinetsbay/site/issues/590
use Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags as Tags;
final class Ebizmarts_MailChimp_Model_ProcessWebhook {
	/**
	 * 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_ProcessWebhook`": https://github.com/cabinetsbay/site/issues/590
	 * @used-by Aoe_Scheduler_Model_Observer::dispatch() (app/code/community/Ebizmarts/MailChimp/etc/config.xml)
	 */
	function p():void {
		$collection = Mage::getModel('mailchimp/webhookrequest')->getCollection();
		$collection->addFieldToFilter('processed', array('eq' => 0));
		$collection->getSelect()->limit(self::BATCH_LIMIT);
		foreach ($collection as $webhookRequest) {
			$data = hcg_mc_h()->unserialize($webhookRequest->getDataRequest());
			if ($data) {
				switch ($webhookRequest->getType()) {
					case 'profile':
						$tags = new Tags;
						$tags->processMergeFields($data);
						break;
					case 'subscribe':
						try {
							$tags = new Tags;
							$tags->processMergeFields($data, true);
						} catch (Exception $e) {
							Mage::logException($e);
						}
						break;
					case 'unsubscribe':
						$this->_unsubscribe($data);
						break;
					case 'cleaned':
						$this->_clean($data);
						break;
					case 'upemail':
						$this->_updateEmail($data);
				}
			}
			$this->_saveProcessedWebhook($webhookRequest);
		}
	}

	/**
	 * Add "Cleaned Emails" notification to Adminnotification Inbox <cleaned>
	 *
	 * @param array $data
	 * @return void
	 */
	private function _clean(array $data):void {
		//Delete subscriber from Magento
		$helper = hcg_mc_h();
		$s = $helper->loadListSubscriber($data['list_id'], $data['email']);

		if ($s && $s->getId()) {
			try {
				$s->delete();
			} catch (Exception $e) {
				Mage::logException($e);
			}
		}
	}

	/**
	 * @param $webhookRequest
	 */
	private function _saveProcessedWebhook($webhookRequest):void {$webhookRequest->setProcessed(1)->save();}

	/**
	 * Update customer email <upemail>
	 *
	 * @param array $data
	 * @return void
	 */
	private function _updateEmail(array $data):void {
		$helper = hcg_mc_h();
		$listId = $data['list_id'];
		$old = $data['old_email'];
		$new = $data['new_email'];

		$oldSubscriber = $helper->loadListSubscriber($listId, $old);
		$newSubscriber = $helper->loadListSubscriber($listId, $new);

		if ($oldSubscriber) {
			if (!$newSubscriber->getId()) {
				if ($oldSubscriber->getId()) {
					$oldSubscriber->setSubscriberEmail($new);
					$oldSubscriber->setSubscriberSource(Ebizmarts_MailChimp_Model_Subscriber::MAILCHIMP_SUBSCRIBE);
					$oldSubscriber->save();
				} else {
					$helper->subscribeMember($newSubscriber);
				}
			}
		}
	}

	/**
	 * Unsubscribe or delete email from Magento list, store aware
	 *
	 * @param array $data
	 * @return void
	 */
	private function _unsubscribe(array $data):void {
		$helper = hcg_mc_h();
		$subscriber = $helper->loadListSubscriber($data['list_id'], $data['email']);
		if ($subscriber && $subscriber->getId()) {
			try {
				$action = isset($data['action']) ? $data['action'] : 'delete';
				$subscriberStatus = $subscriber->getSubscriberStatus();
				$statusUnsubscribed = Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED;

				switch ($action) {
				case 'delete':
					//if config setting "Webhooks Delete action" is set as "Delete customer account"

					if (Mage::getStoreConfig(
						Ebizmarts_MailChimp_Model_Config::GENERAL_UNSUBSCRIBE, $subscriber->getStoreId()
					)
					) {
						$subscriber->delete();
					} elseif ($subscriberStatus != $statusUnsubscribed) {
						$helper->unsubscribeMember($subscriber);
					}
					break;
				case 'unsub':
					if ($subscriberStatus != $statusUnsubscribed) {
						$helper->unsubscribeMember($subscriber);
					}
					break;
				}
			} catch (Exception $e) {
				Mage::logException($e);
			}
		}
	}

	const BATCH_LIMIT = 200;

	/**
	 * Webhooks request url path
	 *
	 * @const string
	 */

	const WEBHOOKS_PATH = 'mailchimp/webhook/index/';
}
