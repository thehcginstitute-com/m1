<?php
use HCG\MailChimp\Tags\ProcessMergeFields;
# 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `Ebizmarts_MailChimp_Model_ProcessWebhook`": https://github.com/thehcginstitute-com/m1/issues/590
final class Ebizmarts_MailChimp_Model_ProcessWebhook {
	/**
	 * 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_ProcessWebhook`": https://github.com/thehcginstitute-com/m1/issues/590
	 * @used-by Aoe_Scheduler_Model_Observer::dispatch() (app/code/community/Ebizmarts/MailChimp/etc/config.xml)
	 */
	function p():void {
		$collection = Mage::getModel('mailchimp/webhookrequest')->getCollection();
		$collection->addFieldToFilter('processed', ['eq' => 0]);
		$collection->getSelect()->limit(self::BATCH_LIMIT);
		foreach ($collection as $webhookRequest) {
			if ($d = hcg_mc_h()->unserialize($webhookRequest->getDataRequest())) {
				switch ($webhookRequest->getType()) {
					case 'profile':
						ProcessMergeFields::p($d);
						break;
					case 'subscribe':
						try {ProcessMergeFields::p($d, true);}
						catch (Exception $e) {Mage::logException($e);}
						break;
					case 'unsubscribe':
						$this->_unsubscribe($d);
						break;
					case 'cleaned':
						$this->_clean($d);
						break;
					case 'upemail':
						$this->_updateEmail($d);
				}
			}
			$this->_saveProcessedWebhook($webhookRequest);
		}
	}


	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_ProcessWebhook`": https://github.com/thehcginstitute-com/m1/issues/590
	 * @used-by self::p()
	 */
	private function _clean(array $data):void {
		//Delete subscriber from Magento
		$s = hcg_mc_sub($data['list_id'], $data['email']);
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
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_ProcessWebhook`": https://github.com/thehcginstitute-com/m1/issues/590
	 * @used-by self::p()
	 */
	private function _updateEmail(array $data):void {
		$listId = $data['list_id'];
		$old = $data['old_email'];
		$new = $data['new_email'];

		$oldSubscriber = hcg_mc_sub($listId, $old);
		$newSubscriber = hcg_mc_sub($listId, $new);

		if ($oldSubscriber) {
			if (!$newSubscriber->getId()) {
				if ($oldSubscriber->getId()) {
					$oldSubscriber->setSubscriberEmail($new);
					$oldSubscriber->setSubscriberSource(Ebizmarts_MailChimp_Model_Subscriber::MAILCHIMP_SUBSCRIBE);
					$oldSubscriber->save();
				}
				else {
					hcg_mc_subscribe($newSubscriber);
				}
			}
		}
	}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_ProcessWebhook`": https://github.com/thehcginstitute-com/m1/issues/590
	 * @used-by self::p()
	 */
	private function _unsubscribe(array $data):void {
		$subscriber = hcg_mc_sub($data['list_id'], $data['email']);
		if ($subscriber && $subscriber->getId()) {
			try {
				$action = isset($data['action']) ? $data['action'] : 'delete';
				$subscriberStatus = $subscriber->getSubscriberStatus();
				$statusUnsubscribed = Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED;
				switch ($action) {
				case 'delete':
					//if config setting "Webhooks Delete action" is set as "Delete customer account"
					if (df_cfg(Ebizmarts_MailChimp_Model_Config::GENERAL_UNSUBSCRIBE, $subscriber->getStoreId())) {
						$subscriber->delete();
					}
					elseif ($subscriberStatus != $statusUnsubscribed) {
						hcg_mc_unsubscribe($subscriber);
					}
					break;
				case 'unsub':
					if ($subscriberStatus != $statusUnsubscribed) {
						hcg_mc_unsubscribe($subscriber);
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
