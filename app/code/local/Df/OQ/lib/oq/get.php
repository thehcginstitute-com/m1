<?php
use Mage_Sales_Model_Order as O;
use Mage_Sales_Model_Quote as Q;

/**
 * 2024-06-02
 * @uses Mage_Sales_Model_Order::getCustomerEmail()
 * @uses Mage_Sales_Model_Quote::getCustomerEmail()
 * 2) The customer's email can be absent in a quote.
 * 3) 2024-06-02 "Port `df_oq_email()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/631
 * @used-by df_subscriber()
 * @param O|Q $oq
 */
function df_oq_email($oq):?string {return $oq->getCustomerEmail();}