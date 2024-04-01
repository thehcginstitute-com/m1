<?php
# 2024-04-01
namespace HCG\Sales;
final class C {
	/**
	 * 2024-04-01
	 * "Confirmation emails are sometimes sent twice for the same order": https://github.com/thehcginstitute-com/m1/issues/538
	 * @used-by \Mage_Sales_Model_Order::queueNewOrderEmail()
	 */
	const ADMINISTRATIVE_EMAILS = [
		'admin@mage2.pro'
		,'contact@thehcginstitute.com'
		,'info@thehcginstitute.com'
		,'sales@thehcginstitute.com'
	];
}