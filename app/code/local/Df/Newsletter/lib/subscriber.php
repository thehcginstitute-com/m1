<?php
use Mage_Customer_Model_Customer as C;
use Mage_Newsletter_Model_Resource_Subscriber_Collection as SC;
use Mage_Newsletter_Model_Subscriber as S;
use Mage_Sales_Model_Order as O;
use Mage_Sales_Model_Quote as Q;

/**
 * 2024-06-02
 * 1) "Implement `df_subscriber()`": https://github.com/thehcginstitute-com/m1/issues/627
 * 2) https://3v4l.org/tIHdP
 * @used-by df_subscriber_c()
 * @used-by Ebizmarts_MailChimp_Block_Adminhtml_Customer_Edit_Tab_Mailchimp::interests()
 * @used-by Ebizmarts_MailChimp_Block_Checkout_Subscribe::_toHtml()
 * @used-by Ebizmarts_MailChimp_Block_Checkout_Success_Groups::getInterest()
 * @used-by Ebizmarts_MailChimp_Block_Customer_Newsletter_Index::getInterest()
 * @used-by Ebizmarts_MailChimp_Block_Popup_Emailcatcher::_handleCookie()
 * @used-by Ebizmarts_MailChimp_GroupController::indexAction()
 * @used-by Ebizmarts_MailChimp_Model_Api_Customers::createBatchJson()
 * @used-by Ebizmarts_MailChimp_Model_Api_Orders::GeneratePOSTPayload()
 * @used-by Ebizmarts_MailChimp_Model_Observer::createCreditmemo()
 * @param string|C|O|Q|null $v [optional]
 */
function df_subscriber($v = null):S {
	$r = Mage::getModel('newsletter/subscriber'); /** @var S $r */
	return !$v ? $r : (
		df_is_email($v) ? $r->loadByEmail($v) : (
			$v instanceof C ? $r->loadByCustomer($v) : (
				/**
				 * 2024-06-02
				 * @used-by Mage_Sales_Model_Order::getCustomerEmail()
				 * @uses Mage_Sales_Model_Quote::getCustomerEmail()
				 */
				df_is_oq($v) ? $r->loadByEmail($v->getCustomerEmail()) : df_error(['v' => $v])
			)
		)
	);
}

/**
 * 2024-06-02 "Implement `df_subscriber_c()`": https://github.com/thehcginstitute-com/m1/issues/626
 * @used-by Glew_Service_Model_Types_Subscribers::load()
 */
function df_subscriber_c():SC {return df_subscriber()->getCollection();}