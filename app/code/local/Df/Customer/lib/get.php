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
 * @used-by df_customer_id()
 * @used-by df_sentry_m()
 * @used-by hcg_mc_customer()
 * @used-by \HCG\MailChimp\Tags::c() (https://github.com/thehcginstitute-com/m1/issues/589)
 * @param string|int|C|Sub|null $v [optional]
 * @param Closure|bool|mixed $onE [optional]
 */
function df_customer($v = null, $onE = null):?C {return df_try(function() use($v):?C {/** @var ?C $r */
	if ($v instanceof C) {
		$r = $v;
	}
	elseif (!$v) {
		df_assert(!df_is_backend());
		$s = df_customer_session();
		df_assert($s->isLoggedIn());
		$r = df_customer($s->getId());
	}
	else {
		$r = Mage::getModel('customer/customer');
		if (df_is_email($v)) {
			$r->loadByEmail($v);
		}
		else {
			$r->load(df_assert(
				$v instanceof O ? $v->getCustomerId() : (
					is_int($v) || is_string($v) ? $v : ($v instanceof Sub ? $v->getCustomerId() : null)
				)
				# 2024-05-20
				# "Provide an ability to specify a context for a `Df\Core\Exception` instance":
				# https://github.com/mage2pro/core/issues/375
				,df_error_create("Unable to detect the customer's ID", ['v' => $v])
			));
		}
		df_assert($r->getId());
	}
	return $r
;}, $onE);}