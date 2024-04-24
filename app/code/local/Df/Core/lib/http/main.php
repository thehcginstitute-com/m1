<?php
/**
 * 2024-01-09 "Port `df_request()`": https://github.com/thehcginstitute-com/m1/issues/141
 * @used-by Ebizmarts_MailChimp_Block_Adminhtml_Customer_Edit_Tab_Mailchimp::interests() (https://github.com/thehcginstitute-com/m1/issues/579)
 * @used-by Ebizmarts_MailChimp_Model_Observer::addCustomerTab() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by app/code/community/INT/DisplayCvv/Block/Payment/Info/Ccsave.php
 * @param string|string[] $k [optional]
 * @param string|null|callable $d [optional]
 * @return string|array(string => string)
 */
function df_request($k = '', $d = null) {$o = df_request_o(); return df_nes($k) ? $o->getParams() : (
	is_array($k) ? dfa($o->getParams(), $k) : df_if1(df_nes($r = $o->getParam($k)), $d, $r)
);}

/**
 * 2016-12-25
 * The @uses \Laminas\Http\Request::getHeader() method is insensitive to the argument's letter case:
 * @see \Laminas\Http\Headers::createKey()
 * https://github.com/zendframework/zendframework/blob/release-2.4.6/library/Zend/Http/Headers.php#L462-L471
 * @used-by df_request_ua()
 * @param string $k
 * @return string|false
 */
function df_request_header($k) {return df_request_o()->getHeader($k);}

/**
 * 2021-06-05
 * @used-by df_context()
 * @return string
 */
function df_request_method() {return dfa($_SERVER, 'REQUEST_METHOD');}

/**
 * 2015-08-14 https://github.com/magento/magento2/issues/1675
 * @used-by df_action_name()
 * @used-by df_context()
 * @used-by df_is_ajax()
 * @used-by df_request()
 * @used-by df_request_header()
 * @used-by df_rp_has()
 * @used-by INT\DisplayCvv\B::_prepareSpecificInformation() (https://github.com/thehcginstitute-com/m1/issues/142)
 * @return Mage_Core_Controller_Request_Http
 */
function df_request_o() {return Mage::app()->getRequest();}
