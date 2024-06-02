<?php
use Mage_Sales_Model_Order as O;
use Mage_Sales_Model_Order_Address as A;
/**
 * 2025-05-16 "Implement `df_oa()`": https://github.com/thehcginstitute-com/m1/issues/616
 * @see Mage_Sales_Model_Order::getBillingAddress()
 * @see Mage_Sales_Model_Order::getShippingAddress()
 * @used-by \HCG\MailChimp\Tags::address() (https://github.com/thehcginstitute-com/m1/issues/589)
 */
function df_oa(O $o, string $t):?A {
	df_assert_address_type($t);
	return df_find($o->getAddressesCollection(), function(A $a) use($t):bool {return
		!$a->isDeleted() && $t === $a->getAddressType()
	;});
}