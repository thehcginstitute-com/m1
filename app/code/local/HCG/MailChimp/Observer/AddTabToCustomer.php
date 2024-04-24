<?php
namespace HCG\MailChimp\Observer;
use Mage_Adminhtml_Block_Customer_Edit_Tabs as CustomerTabs;
use Mage_Customer_Model_Customer as C;
use Varien_Event_Observer as O;
# 2024-04-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `Ebizmarts_MailChimp_Model_Observer`": https://github.com/thehcginstitute-com/m1/issues/580
final class AddTabToCustomer {
	/**
	 * 2024-04-24
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see app/code/community/Ebizmarts/MailChimp/etc/config.xml
	 */
	function p(O $o) {
        if (($b = $o->getEvent()->getBlock()) instanceof CustomerTabs) { /** @var CustomerTabs $b */
            $c = \Mage::getModel('customer/customer')->load(df_request('id')); /** @var C $c */
			# 2024-04-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# "Delete the `->getMailchimpStoreView()` / `mailchimp_store_view` calls for `Mage_Customer_Model_Customer`
			# because it always returns `NULL`": https://github.com/thehcginstitute-com/m1/issues/578
            if (hcg_mc_h()->getLocalInterestCategories((int)$c->getStoreId())
                && (df_request('type') || 'edit' === df_request_o()->getActionName())
            ) {
                $b->addTab('mailchimp', [
					'class' => 'ajax'
					,'label' => 'MailChimp'
					,'url' => $b->getUrl('adminhtml/mailchimp/index', ['_current' => true])
				]);
			}
        }
	}
}