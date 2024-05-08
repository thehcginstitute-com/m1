<?php
use Mage_Sales_Model_Resource_Order_Collection as C;
/**
 * 2019-11-20
 * 2024-05-04 "Port `df_order_c()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/591
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags::o() (https://github.com/cabinetsbay/site/issues/589)
 */
function df_order_c():C {return Mage::getResourceModel('sales/order_collection');}