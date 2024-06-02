<?php
use Closure as F;
use Mage_Directory_Model_Country as C;

/**
 * 2016-05-20
 * It returns the country name name for an ISO 3166-1 alpha-2 2-characher code and locale
 * (or the default system locale) given: https://ru.wikipedia.org/wiki/ISO_3166-1
 * 2024-03-31 "Port `df_country_ctn()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/536
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags::attOther() (https://github.com/thehcginstitute-com/m1/issues/589)
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags::vAddress() (https://github.com/thehcginstitute-com/m1/issues/589)
 * @used-by IWD_OrderManager_Adminhtml_Sales_AddressController::format() (https://github.com/thehcginstitute-com/m1/issues/533)
 * @param F|bool|mixed $onE [optional]
 */
function df_country_ctn(string $c, $onE = true):string { return df_try(function() use($c):string {
	$o = Mage::getModel('directory/country'); /** @var C $o */
	$o->loadByCode($c);
	return $o->getName();
}, $onE);}