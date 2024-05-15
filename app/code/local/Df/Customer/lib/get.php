<?php
use Mage_Customer_Model_Customer as C;
use Mage_Sales_Model_Order as O;

/**
 * 2016-04-05
 * How to get a customer by his ID? https://mage2.pro/t/1136
 * How to get a customer by his ID with the @uses \Magento\Customer\Model\CustomerRegistry::retrieve()?
 * https://mage2.pro/t/1137
 * How to get a customer by his ID with the @see \Magento\Customer\Api\CustomerRepositoryInterface::getById()?
 * https://mage2.pro/t/1138
 * 2017-06-14 The $throw argument is not used for now.
 * 2024-05-16 "df_customer `STUB()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/612
 * @used-by df_ci_get()
 * @used-by df_ci_get()
 * @used-by df_ci_save()
 * @used-by df_customer()
 * @used-by df_sentry_m()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags::c() (https://github.com/cabinetsbay/site/issues/589)
 * @param string|int|C|null $c [optional]
 * @param Closure|bool|mixed $onE [optional]
 * @return C|null
 */
function df_customer($c = null, $onE = null) {return df_try(function() use($c) {return
	/** @var int|string|null $id */
	/**
	 * 2016-08-22
	 * I do not use @see \Magento\Customer\Model\Session::getCustomer()
	 * because it does not use the customers repository, and loads a customer directly from the database.
	 */
	!$c ? (
		df_customer_session()->isLoggedIn()
			? df_customer(df_customer_id())
			: df_error('df_customer(): the argument is null and the visitor is anonymous.')
	) : ($c instanceof C ? $c : (
		($id =
			$c instanceof O ? $c->getCustomerId() : (
				is_int($c) || is_string($c) ? $c : null
			)
		)
			? Mage::getModel('customer/customer')->load($id)
			: df_error('df_customer(): the argument of type %s is unrecognizable.', df_type($c))
	))
;}, $onE);}