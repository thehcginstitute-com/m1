<?php
/**
 * 2015-01-28
 * 2016-07-31
 * К сожалению, мы не можем указывать кодировку в обработчике, установленном @see set_exception_handler(),
 * потому что @see set_exception_handler() в Magento работать не будет
 * из-за глобального try..catch в методе @see Mage::run()
 * @used-by df_error()
 * @used-by df_error_html()
 */
function df_header_utf():void {df_is_cli() || headers_sent() ?: header('Content-Type: text/html; charset=UTF-8');}

/**
 * 2015-11-27
 * Note 1.
 * Google API в случае сбоя возвращает корректный JSON, но с кодом HTTP 403,
 * что приводит к тому, что @uses file_get_contents() не просто возвращает JSON,
 * а создаёт при этом @see E_WARNING.
 * Чтобы при коде 403 warning не создавался, использую ключ «ignore_errors»:
 * https://php.net/manual/context.http.php#context.http.ignore-errors
 * http://stackoverflow.com/a/21976746
 * Note 2.
 * Обратите внимание, что для использования @uses file_get_contents
 * с адресами https требуется расширение php_openssl интерпретатора PHP,
 * однако оно является системным требованием Magento 2:
 * http://devdocs.magento.com/guides/v2.0/install-gde/system-requirements.html#required-php-extensions
 * Поэтому мы вправе использовать здесь @uses file_get_contents
 * Note 3. The function returns the read data or FALSE on failure. https://php.net/manual/function.file-get-contents.php
 * 2016-05-31
 * Стандартное время ожидание ответа сервера задаётся опцией default_socket_timeout:
 * https://php.net/manual/filesystem.configuration.php#ini.default-socket-timeout
 * Её значение по-умолчанию равно 60 секундам.
 * Конечно, при оформлении заказа негоже заставлять покупателя ждать 60 секунд
 * только ради узнавания его страны вызовом @see df_visitor()
 * Поэтому добавил возможность задавать нестандартное время ожидания ответа сервера:
 * http://stackoverflow.com/a/10236480
 * https://amitabhkant.com/2011/08/21/using-timeouts-with-file_get_contents-in-php/
 * @used-by df_http_json()
 * @param array(string => string) $query [optional]
 * @param Closure|bool|mixed $onE [optional]
 */
function df_http_get(string $url, array $query = [], int $timeout = 0, $onE = true):string {return df_contents(
	!$query ? $url : $url . '?' . http_build_query($query)
	,$onE
	,stream_context_create(['http' => df_clean(['ignore_errors' => true, 'timeout' => $timeout])])
);}

/**
 * 2024-01-09 "Port `df_request()`": https://github.com/thehcginstitute-com/m1/issues/141
 * @used-by Ebizmarts_MailChimp_Block_Adminhtml_Customer_Edit_Tab_Mailchimp::interests() (https://github.com/thehcginstitute-com/m1/issues/579)
 * @used-by Ebizmarts_MailChimp_Model_Observer::addCustomerTab() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by HCG\MailChimp\Observer\AddTabToCustomer::p() (https://github.com/thehcginstitute-com/m1/issues/580)
 * @used-by INT\DisplayCvv\B::_prepareSpecificInformation() (https://github.com/thehcginstitute-com/m1/issues/142)
 * @used-by app/design/adminhtml/default/default/template/sales/order/view/info.phtml (https://github.com/thehcginstitute-com/m1/issues/669)
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
 * @used-by HCG\MailChimp\Observer\AddTabToCustomer::p() (https://github.com/thehcginstitute-com/m1/issues/580)
 * @used-by INT\DisplayCvv\B::_prepareSpecificInformation() (https://github.com/thehcginstitute-com/m1/issues/142)
 * @return Mage_Core_Controller_Request_Http
 */
function df_request_o() {return Mage::app()->getRequest();}