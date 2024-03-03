<?php
/**
 * 2024-03-03
 * "Implement the `df_customer_group()` function": https://github.com/thehcginstitute-com/m1/issues/446
 * @used-by df_customer_is_anon()
 * @used-by hcg_customer_is_new()
 * @used-by hcg_is_patient()
 */
function df_customer_group():int {return (int)df_customer_session()->getCustomerGroupId();}