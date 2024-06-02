<?php
use Mage_Customer_Model_Customer as C;
use Mage_Newsletter_Model_Resource_Subscriber_Collection as SC;
use Mage_Newsletter_Model_Subscriber as S;
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
 * @param string|C|null $id [optional]
 */
function df_subscriber($id = null):S {
	$r = Mage::getModel('newsletter/subscriber'); /** @var S $r */
	return !$id ? $r : (
		df_is_email($id) ? $r->loadByEmail($id) : (
			$id instanceof C ? $r->loadByCustomer($id) : df_error(['id' => $id])
		)
	);
}

/**
 * 2024-06-02 "Implement `df_subscriber_c()`": https://github.com/thehcginstitute-com/m1/issues/626
 * @used-by Glew_Service_Model_Types_Subscribers::load()
 */
function df_subscriber_c():SC {return df_subscriber()->getCollection();}