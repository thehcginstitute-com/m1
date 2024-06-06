<?php
use Mage_Customer_Model_Customer as C;
use Mage_Newsletter_Model_Subscriber as S;
/**
 * 2024-06-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
 * @used-by Ebizmarts_MailChimp_Model_Observer::newOrder()
 * @used-by Ebizmarts_MailChimp_Model_ProcessWebhook::_clean()
 * @used-by Ebizmarts_MailChimp_Model_ProcessWebhook::_updateEmail()
 * @used-by Ebizmarts_MailChimp_Model_ProcessWebhook::_unsubscribe()
 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::p()
 */
function hcg_mc_sub($listId, string $email):S {/** @var S $r */
	if (!($r = df_subscriber($email))->getId()) {
		$r->setEmail($email);
		/** @var ?C $c */
		if (!($c = df_customer($email))) {
			# No customer with that address.
			# Just assume the first store ID is the correct one
			# (as there is no other way to tell which store this MailChimp list guest subscriber belongs to).
			$r->setStoreId(hcg_mc_store_id($listId) ?: 0);
		}
		else {
			$r->setStoreId($c->getStoreId());
			$r->setCustomerId($c->getId());
		}
	}
	return $r;
}

/**
 * 2024-06-06 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
 * @used-by hcg_mc_subscribe()
 * @used-by hcg_mc_unsubscribe()
 */
function hcg_mc_sub_update(S $s):void {
	$s->setImportMode(true);
	$s['subscriber_source'] = Ebizmarts_MailChimp_Model_Subscriber::MAILCHIMP_SUBSCRIBE;
	$s->setIsStatusChanged(true);
	$s->save();
}

/**
 * 2024-06-06 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
 * @used-by Ebizmarts_MailChimp_Model_ProcessWebhook::_updateEmail()
 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::_addSubscriberData()
 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::p()
 */
function hcg_mc_subscribe(S $s):void {
	$s->setStatus(S::STATUS_SUBSCRIBED);
	$s->setSubscriberConfirmCode($s->randomSequence());
	hcg_mc_sub_update($s);
}

/**
 * 2024-06-06 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
 * @used-by Ebizmarts_MailChimp_Model_Observer::createCreditmemo()
 * @used-by Ebizmarts_MailChimp_Model_ProcessWebhook::_unsubscribe()
 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::_addSubscriberData()
 */
function hcg_mc_unsubscribe(S $s):void {
	$s->setStatus(S::STATUS_UNSUBSCRIBED);
	hcg_mc_sub_update($s);
}