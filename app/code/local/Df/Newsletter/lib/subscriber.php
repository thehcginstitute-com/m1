<?php
use Mage_Newsletter_Model_Subscriber as S;
use Mage_Newsletter_Model_Resource_Subscriber_Collection as C;
/**
 * 2024-06-02 "Implement `df_subscriber()`": https://github.com/thehcginstitute-com/m1/issues/627
 * @used-by df_subscriber_c()
 * @used-by Ebizmarts_MailChimp_Block_Checkout_Subscribe::_toHtml()
 * @used-by Ebizmarts_MailChimp_Block_Popup_Emailcatcher::_handleCookie()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 */
function df_subscriber():S {return Mage::getModel('newsletter/subscriber');}

/**
 * 2024-06-02 "Implement `df_subscriber_c()`": https://github.com/thehcginstitute-com/m1/issues/626
 * @used-by Glew_Service_Model_Types_Subscribers::load()
 */
function df_subscriber_c():C {return df_subscriber()->getCollection();}