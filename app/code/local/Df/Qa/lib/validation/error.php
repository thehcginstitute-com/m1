<?php
use Df\Core\Exception as DFE;
use Throwable as T; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2024-03-16 "Port `df_error` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/487
 * @used-by df_address_is_billing()
 * @used-by df_ar()
 * @used-by df_assert()
 * @used-by df_assert_assoc()
 * @used-by df_assert_between()
 * @used-by df_assert_class_exists()
 * @used-by df_assert_count()
 * @used-by df_assert_eq()
 * @used-by df_assert_gd()
 * @used-by df_assert_ge()
 * @used-by df_assert_gt()
 * @used-by df_assert_https()
 * @used-by df_assert_in()
 * @used-by df_assert_le()
 * @used-by df_assert_leaf()
 * @used-by df_assert_lt()
 * @used-by df_assert_ne()
 * @used-by df_assert_nef()
 * @used-by df_assert_oq()
 * @used-by df_assert_iterable()
 * @used-by df_asset_url()
 * @used-by df_bool()
 * @used-by df_call()
 * @used-by df_caller_m()
 * @used-by df_con_hier_suf()
 * @used-by df_con_hier_suf_ta()
 * @used-by df_con_s()
 * @used-by df_contents()
 * @used-by df_country()
 * @used-by df_country_ctn()
 * @used-by df_date_from_db()
 * @used-by df_fe_m()
 * @used-by df_file_name()
 * @used-by df_float()
 * @used-by df_int()
 * @used-by df_invoice_by_trans()
 * @used-by df_json_decode()
 * @used-by df_leaf_sne()
 * @used-by df_load()
 * @used-by df_mail_shipment()
 * @used-by df_module_file()
 * @used-by df_not_implemented()
 * @used-by df_oq()
 * @used-by df_oq_currency_c()
 * @used-by df_oq_sa()
 * @used-by df_oq_shipping_amount()
 * @used-by df_oq_shipping_desc()
 * @used-by df_oqi_is_leaf()
 * @used-by df_oqi_qty()
 * @used-by df_order()
 * @used-by df_order_last()
 * @used-by df_oro_get_list()
 * @used-by df_pad()
 * @used-by df_product_current()
 * @used-by df_quote_id()
 * @used-by df_route()
 * @used-by df_sentry_m()
 * @used-by df_sprintf_strict()
 * @used-by df_string()
 * @used-by df_subscriber()
 * @used-by df_try()
 * @used-by df_xml_children()
 * @used-by df_xml_parse()
 * @used-by dfa_assert_keys()
 * @used-by dfaf()
 * @used-by dfc()
 * @used-by dfp()
 * @used-by dfp_due()
 * @used-by dfp_oq()
 * @used-by dfp_refund()
 * @used-by dfpex_args()
 * @used-by dfpm()
 * @used-by dfs_con()
 * @used-by Df\Qa\Method::throwException()
 * @used-by Mage_Eav_Model_Attribute_Data_Abstract::_applyInputFilter()
 * @used-by Varien_Data_Form_Filter_Date::inputFilter()
 * @param string|string[]|array(string => mixed)|mixed|T|null ...$a
 * @throws DFE
 */
function df_error(...$a):void {
	# 2024-05-22 "Implement `Df\Core\Exception::throw_()`": https://github.com/mage2pro/core/issues/386
	df_error_create(...$a)->throw_();
	/**
	 * 2020-02-15
	 * 1) "The Cron log (`magento.cron.log`) should contain a backtrace for every exception logged":
	 * https://github.com/tradefurniturecompany/site/issues/34
	 * 2) The @see \Exception 's backtrace is set when the exception is created, not when it is thrown:
	 * https://3v4l.org/qhd7m
	 * So we have a correct backtrace even without throwing the exception.
	 * 2020-02-17 @see \Df\Cron\Plugin\Console\Command\CronCommand::aroundRun()
	 * 2024-03-03
	 * 1) "An endless loop `df_error` → `df_log` → … → `df_module_file_name` → … → `df_error`":
	 * https://github.com/mage2pro/core/issues/353
	 * 2) The previous code:
	 * 		if (df_is_cron()) {
	 * 			df_no_rec(function() use($e):void {df_log($e, 'Df_Cron');});
	 * 		}
	 * https://github.com/mage2pro/core/blob/10.6.4/Qa/lib/validation/error.php#L164-L169
	 * 3) We do not need to log the exception here because it will be logged in 2 places:
	 * 3.1) @see \Df\Cron\Plugin\Console\Command\CronCommand::aroundRun()
	 * https://github.com/mage2pro/core/blob/10.6.4/Cron/Plugin/Console/Command/CronCommand.php#L19-L27
	 * 3.2) @see \Magento\Framework\Console\Cli::doRun():
	 * 		try {
	 * 			$exitCode = parent::doRun($input, $output);
	 * 		}
	 * 		catch (\Exception $e) {
	 * 			$errorMessage = $e->getMessage() . PHP_EOL . $e->getTraceAsString();
	 * 			$this->logger->error($errorMessage);
	 * 			$this->initException = $e;
	 * 		}
	 * https://github.com/magento/magento2/blob/2.4.7-beta2/lib/internal/Magento/Framework/Console/Cli.php#L115-L121
	 * `$this->logger->error($errorMessage);` will be logged by @see \Df\Framework\Log\Dispatcher::handle()
	 * 4) "The required file «vendor/mage2pro/core/Cron/etc/df.json» is absent":
	 * https://github.com/mage2pro/core/issues/352
	 * @see df_module_file_name()
	 * https://github.com/mage2pro/core/blob/10.6.4/Framework/lib/module/fs/name.php#L79-L84
	 * This error should not be logged at all, because `df_module_file_name()` is called with `$onE = false`,
	 * and `Df_Core` module is used instead of `Df_Cron`: @see df_sentry_m().
	 */
}

/**
 * 2016-07-31
 * 2024-03-16 "Port `df_error_create` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/488
 * 2024-05-20 "Provide an ability to specify a context for a `Df\Core\Exception` instance":
 * https://github.com/mage2pro/core/issues/375
 * @used-by df_customer()
 * @used-by df_error()
 * @used-by df_error_html()
 * @used-by \HCG\MailChimp\Tags::attCustomer() (https://github.com/thehcginstitute-com/m1/issues/589)
 * @param mixed ...$a
 */
function df_error_create(...$a):DFE {/** @var DFE $r */
	$a0 = dfa($a, 0); /** @var string|string[]|mixed|T|null $a0 */
	$tail = df_tail($a); /** @var mixed[] $tail */
	$tailC = count($tail); /** @var int $tailC */
	/** @var mixed|null|array(string => mixed) $a1 */ /** @var bool $hasContext */
	if (!df_is_assoc($a0)) {
		$a1 = df_first($tail);
		$hasContext = 2 > $tailC && (is_null($a1) || df_is_assoc($a1));
	}
	else {
		df_assert_eq(0, $tailC);
		[$a0, $a1] = [null, $a0];
		$hasContext = true;
	}
	/** @var array(string => mixed)|null $context */
	$context = !$hasContext ? [] : df_eta($a1);  /** @var array(string => mixed) $context */
	if (df_is_th($a0)) {
		df_assert($hasContext);
		$r = DFE::wrap($a0, $context);
	}
	else {
		if (is_array($a0)) {
			$a0 = df_cc_n($a0);
		}
		if (is_string($a0) && !$hasContext) {
			$a0 = df_format($a);
		}
		$r = new DFE($a0);
		if ($hasContext) {
			$r->context($context);
		}
	}
	return $r;
}