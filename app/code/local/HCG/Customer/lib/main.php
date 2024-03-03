<?php
/**
 * 2024-03-03 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Implement the `hcg_customer_is_new()` function` template": https://github.com/thehcginstitute-com/m1/issues/442
 * @used-by app/design/frontend/default/mobileshoppe/template/customer/account/navigation.phtml
 */
function hcg_customer_is_new():bool {return 4 === (int)df_customer_session()->getCustomerGroupId();}

/**
 * 2024-03-03 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Implement the `hcg_is_patient()` function` template": https://github.com/thehcginstitute-com/m1/issues/443
 */
function hcg_is_patient():bool {return 14 === (int)df_customer_session()->getCustomerGroupId();}