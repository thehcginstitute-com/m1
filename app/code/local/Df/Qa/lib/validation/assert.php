<?php
use Df\Core\Exception as DFE;
use Df\Qa\Method as Q;
use Exception as E;
use Throwable as T; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2019-12-14
 * If you do not want the exception to be logged via @see df_bt_log(),
 * then you can pass an empty string (instead of `null`) as the second argument:
 * @see \Df\Core\Exception::__construct():
 *		if (is_null($m)) {
 *			$m = __($prev ? df_xts($prev) : 'No message');
 *			# 2017-02-20 To facilite the «No message» diagnostics.
 *			if (!$prev) {
 *				df_bt_log();
 *			}
 *		}
 * https://github.com/mage2pro/core/blob/5.5.7/Core/Exception.php#L61-L67
 * @used-by df_assert_module_enabled()
 * @used-by df_assert_qty_supported()
 * @used-by df_call_parent()
 * @used-by df_caller_m()
 * @used-by df_caller_mf()
 * @used-by df_catalog_locator()
 * @used-by df_config_field()
 * @used-by df_configurable_children()
 * @used-by df_customer()
 * @used-by df_date_from_timestamp_14()
 * @used-by df_dtss()
 * @used-by df_error_create()
 * @used-by df_eta()
 * @used-by df_fe_fs()
 * @used-by df_float()
 * @used-by df_id()
 * @used-by df_layout_update()
 * @used-by df_module_dir()
 * @used-by df_module_file_name()
 * @used-by df_oqi_amount()
 * @used-by df_oqi_amount()
 * @used-by df_xml_child()
 * @used-by dfaf()
 * @used-by dfp_oq()
 * @used-by dfr_prop()
 * @used-by Df\Core\Html\Tag::openTagWithAttributesAsText()
 * @used-by Df\Qa\Trace\Frame::methodParameter()
 * @used-by Df\Qa\Trace\Frame::url()
 * @used-by HCG\MailChimp\Tags::attCustomer() (https://github.com/thehcginstitute-com/m1/issues/589)
 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::mcByCA() (https://github.com/thehcginstitute-com/m1/issues/589)
 * @used-by IWD_OrderManager_Model_Order_Edit::updateOrderItems() (https://github.com/thehcginstitute-com/m1/issues/666)
 * @used-by Mage_Core_Block_Template::fetchView() (https://github.com/thehcginstitute-com/m1/issues/670)
 * @param mixed $cond
 * @param string|string[]|array(string => mixed)|mixed|T|null ...$a
 * @return mixed
 * @throws DFE
 */
function df_assert($cond,  ...$a) {return $cond ?: df_error( ...$a);}


/**
 * 2016-08-09
 * 2024-06-03
 * 1) "Use the `iterable` type": https://github.com/mage2pro/core/issues/403
 * 2) `iterable` is supported by PHP ≥ 7.1: https://3v4l.org/qNX1j
 * 3) https://php.net/manual/en/language.types.iterable.php
 * @used-by dfaf()
 */
function df_assert_iterable(iterable $v):iterable {return $v;}

/**
 * @used-by df_call() (https://github.com/mage2pro/core/issues/435)
 * @used-by \HetNieuweWeb_CustomerNavigation_Block_Customer_Account_Navigation::removeLinkByName()
 * @param mixed $v
 */
function df_bool($v):bool {
	/**
	 * Unfortunately, we can not replace @uses in_array() with @see array_flip() + @see isset() to speedup the execution,
	 * because it could lead to the warning: «Warning: array_flip(): Can only flip STRING and INTEGER values!».
	 * Moreover, @see array_flip() + @see isset() fails the following test:
	 *	$a = array(null => 3, 0 => 4, false => 5);
	 *	$this->assertNotEquals($a[0], $a[false]);
	 * Though, @see array_flip() + @see isset() does not fail the tests:
	 * $this->assertNotEquals($a[null], $a[0]);
	 * $this->assertNotEquals($a[null], $a[false]);
	 */
	static $no = [0, '0', 'false', false, null, 'нет', 'no', 'off', '']; /** @var mixed[] $no */
	static $yes = [1, '1', 'true', true, 'да', 'yes', 'on']; /** @var mixed[] $yes */
	/**
	 * Passing $strict = true to the @uses in_array() call is required here,
	 * otherwise any true-compatible value (e.g., a non-empty string) will pass the check.
	 */
	return in_array($v, $no, true) ? false : (in_array($v, $yes, true) ? true : df_error(
		'A boolean value is expected, but got «%s».', df_dump($v)
	));
}