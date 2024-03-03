<?php
/**
 * 2024-03-03 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Implement the `df_customer_group()` function": https://github.com/thehcginstitute-com/m1/issues/446
 * @used-by hcg_customer_is_new()
 * @used-by hcg_is_patient()
 */
function df_customer_group():int {return (int)df_customer_session()->getCustomerGroupId();}