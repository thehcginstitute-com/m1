<?php
# 2024-04-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `Ebizmarts_MailChimp_Block_Adminhtml_Customer_Edit_Tab_Mailchimp`":
# https://github.com/thehcginstitute-com/m1/issues/579
use Mage_Customer_Model_Customer as C;
use Mage_Newsletter_Model_Subscriber as S;
/** @used-by Ebizmarts_MailChimp_Adminhtml_MailchimpController::indexAction() */
final class Ebizmarts_MailChimp_Block_Adminhtml_Customer_Edit_Tab_Mailchimp extends Mage_Adminhtml_Block_Widget_Grid {
	/**
	 * 2024-04-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Block_Adminhtml_Customer_Edit_Tab_Mailchimp`":
	 * https://github.com/thehcginstitute-com/m1/issues/579
	 * @override
	 * @see Mage_Core_Block_Template::getTemplate()
	 */
	function getTemplate():string {return 'ebizmarts/mailchimp/customer/tab/mailchimp.phtml';}

	/**
	 * 2024-04-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Block_Adminhtml_Customer_Edit_Tab_Mailchimp`":
	 * https://github.com/thehcginstitute-com/m1/issues/579
	 * @used-by app/design/adminhtml/default/default/template/ebizmarts/mailchimp/customer/tab/mailchimp.phtml
	 */
	function interests() {
		$c = Mage::getModel('customer/customer'); /** @var C $c */
		$c->load((int)df_request('id'));
		$s = df_subscriber($c); /** @var S $s */
		# 2024-04-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# 1) "Delete the `->getMailchimpStoreView()` / `mailchimp_store_view` calls for `Mage_Customer_Model_Customer`
		# because it always returns `NULL`": https://github.com/thehcginstitute-com/m1/issues/578
		# 2) "Refactor `Ebizmarts_MailChimp_Block_Adminhtml_Customer_Edit_Tab_Mailchimp`":
		# https://github.com/thehcginstitute-com/m1/issues/579
		return hcg_mc_h()->getInterestGroups($c->getId(), $s->getSubscriberId(), (int)$c->getStoreId());
	}
}
