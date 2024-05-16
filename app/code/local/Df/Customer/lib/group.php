<?php
use Closure as F;
use Mage_Customer_Model_Customer as C;
use Mage_Customer_Model_Group as G;
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
 * 2024-05-16 "Port `df_customer_group_name()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/617
 * @param C|G|int $v
 * @param F|bool|mixed string [optional]
 */
function df_customer_group_name($v, $onE = ''):string {return df_try(function() use($v):string {return
	df_customer_group($v)->getCode()
;}, $onE);}