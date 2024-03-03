<?php
/**
 * 2024-03-03 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Implement the `hcg_customer_is_new()` function` template": https://github.com/thehcginstitute-com/m1/issues/442
 */
function hcg_customer_is_new():bool {return 4 === (int)Mage::getSingleton('customer/session')->getCustomerGroupId();}