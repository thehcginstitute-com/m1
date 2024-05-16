<?php
use Closure as F;
use Mage_Customer_Model_Customer as C;
use Mage_Customer_Model_Group as G;
/**
 * 2020-02-06
 * 2024-05-16 "Port `df_customer_group()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/618
 * @used-by df_customer_group_name()
 * @param C|G|int $v
 */
function df_customer_group($v):G {/** @var G $r */
	if ($v instanceof G) {
		$r = $v;
	}
	else {
		$r = Mage::getModel('customer/group');
		$r->load($v instanceof C ? $v->getGroupId() : $v);
	}
	return $r;
}

/**
 * 2024-03-03
 * "Implement the `df_customer_group_id()` function": https://github.com/thehcginstitute-com/m1/issues/446
 * @used-by df_customer_is_anon()
 * @used-by hcg_customer_is_new()
 * @used-by hcg_is_patient()
 */
function df_customer_group_id():int {return (int)df_customer_session()->getCustomerGroupId();}

/**
 * 2020-02-06
 * 2024-05-16
 * 1) "Port `df_customer_group_name()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/617
 * 2) @see Mage_Adminhtml_Block_Customer_Edit_Tab_View::getGroupName()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags::attCustomer() (https://github.com/cabinetsbay/site/issues/589)
 * @param C|G|int $v
 * @param F|bool|mixed string [optional]
 */
function df_customer_group_name($v, $onE = ''):string {return df_try(function() use($v):string {return
	df_customer_group($v)->getCode()
;}, $onE);}