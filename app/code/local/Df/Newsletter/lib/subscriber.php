<?php
use Mage_Newsletter_Model_Subscriber as S;
use Mage_Newsletter_Model_Resource_Subscriber_Collection as C;
/**
 * 2024-06-02
 * 1) "Implement `df_subscriber()`": https://github.com/thehcginstitute-com/m1/issues/627
 * 2) https://3v4l.org/tIHdP
 * @used-by df_subscriber_c()
 * @used-by Ebizmarts_MailChimp_Block_Checkout_Subscribe::_toHtml()
 * @used-by Ebizmarts_MailChimp_Block_Checkout_Success_Groups::getInterest()
 * @used-by Ebizmarts_MailChimp_Block_Customer_Newsletter_Index::getInterest()
 * @used-by Ebizmarts_MailChimp_Block_Popup_Emailcatcher::_handleCookie()
 * @used-by Ebizmarts_MailChimp_GroupController::indexAction()
 * @used-by Ebizmarts_MailChimp_Model_Api_Customers::createBatchJson()
 * @used-by Ebizmarts_MailChimp_Model_Api_Orders::GeneratePOSTPayload()
 * @used-by Ebizmarts_MailChimp_Model_Observer::createCreditmemo()
 */
function df_subscriber(?string $email = ''):S {
	$r = Mage::getModel('newsletter/subscriber'); /** @var S $r */
	return !$email ? $r : $r->loadByEmail($email);
}

/**
 * 2024-06-02 "Implement `df_subscriber_c()`": https://github.com/thehcginstitute-com/m1/issues/626
 * @used-by Glew_Service_Model_Types_Subscribers::load()
 */
function df_subscriber_c():C {return df_subscriber()->getCollection();}