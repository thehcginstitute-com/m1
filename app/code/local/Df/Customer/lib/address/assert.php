<?php
use Df\Core\Exception as DFE;
use Mage_Customer_Model_Address_Abstract as AA;
use Throwable as T;

/**
 * 2024-05-16
 * 1) "Implement `df_assert_address_type()`": https://github.com/mage2pro/core/issues/372
 * 2) "Port `df_assert_address_type()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/615
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags::address() (https://github.com/cabinetsbay/site/issues/589)
 * @param string|T $m [optional]
 * @throws DFE
 */
function df_assert_address_type(string $t, $m = null):string {return df_assert_in(
	$t, [AA::TYPE_BILLING, AA::TYPE_SHIPPING], $m ?: "Invalid address type: «{$t}»."
);}