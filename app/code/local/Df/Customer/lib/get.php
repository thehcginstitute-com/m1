<?php
use Mage_Customer_Model_Customer as C;
use Mage_Newsletter_Model_Subscriber as Sub;
use Mage_Sales_Model_Order as O;

/**
 * 2016-04-05
 * How to get a customer by his ID? https://mage2.pro/t/1136
 * How to get a customer by his ID with the @uses \Magento\Customer\Model\CustomerRegistry::retrieve()?
 * https://mage2.pro/t/1137
 * How to get a customer by his ID with the @see \Magento\Customer\Api\CustomerRepositoryInterface::getById()?
 * https://mage2.pro/t/1138
 * 2017-06-14 The $throw argument is not used for now.
 * 2024-05-16 "Port `df_customer()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/612
 * 2024-06-02 "Improve `df_customer()`": https://github.com/thehcginstitute-com/m1/issues/632
 * @used-by df_ci_get()
 * @used-by df_ci_get()
 * @used-by df_ci_save()
 * @used-by df_customer()
 * @used-by df_sentry_m()
 * @used-by hcg_mc_customer()
 * @used-by \HCG\MailChimp\Tags::c() (https://github.com/thehcginstitute-com/m1/issues/589)
 * @param string|int|C|Sub|null $v [optional]
 * @param Closure|bool|mixed $onE [optional]
 * @return C|null
 */
function df_customer($v = null, $onE = null) {return df_try(function() use($v) {
	$r =
		/** @var int|string|null $id */
		/**
		 * 2016-08-22
		 * I do not use @see \Magento\Customer\Model\Session::getCustomer()
		 * because it does not use the customers repository, and loads a customer directly from the database.
		 */
		!$v ? (
			df_customer_session()->isLoggedIn()
				? df_customer(df_customer_id())
				: df_error('df_customer(): the argument is `null` and the visitor is anonymous.')
		) : ($v instanceof C ? $v : (
			($id =
				$v instanceof O ? $v->getCustomerId() : (
					is_int($v) || is_string($v) ? $v : ($v instanceof Sub ? $v->getCustomerId() : null)
				)
			)
				? Mage::getModel('customer/customer')->load($id)
				: df_error(['v' => $v])
		))
	; /** @var ?C $r */
	return $r
;}, $onE);}