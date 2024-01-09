<?php
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
 * @return Mage_Core_Controller_Request_Http
 */
function df_request_o() {return Mage::app()->getRequest();}
