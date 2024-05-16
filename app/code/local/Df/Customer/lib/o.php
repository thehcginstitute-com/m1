<?php
use Mage_Customer_Model_Session as S;
use Mage_Customer_Helper_Data as H;

/**
 * 2024-05-16
 * @deprecated It is unused.
 */
function df_customer_h():H {return Mage::helper('customer');}

/**
 * 2024-03-03
 * "Implement the `df_customer_session()` function": https://github.com/thehcginstitute-com/m1/issues/445
 * @used-by df_customer_group_id()
 * @used-by df_customer_id()
 */
function df_customer_session():S {return Mage::getSingleton('customer/session');}