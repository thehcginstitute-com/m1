<?php
use Mage_Customer_Model_Session as S;
/**
 * 2024-03-03 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Implement the `df_customer_session()` function": https://github.com/thehcginstitute-com/m1/issues/445
 * @used-by df_customer_group()
 */
function df_customer_session():S {return Mage::getSingleton('customer/session');}