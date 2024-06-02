<?php
use Mage_Sales_Model_Order as O;
/**
 * 2017-04-10
 * 2024-04-14 "Port `df_is_o()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/562
 * @used-by df_is_oq()
 * @used-by df_oq_currency_c ()
 * @used-by df_oq_customer_name()
 * @used-by df_oq_sa()
 * @used-by df_oqi_leafs()
 * @used-by df_order()
 * @used-by df_quote_id()
 * @used-by df_store()
 * @used-by df_subscriber()
 * @used-by df_visitor()
 * @used-by dfp_due()
 * @param mixed $v
 */
function df_is_o($v):bool {return $v instanceof O;}

/**
 * 2017-04-08
 * 2024-06-02 "Port `df_is_oq()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/629
 * @see df_is_sales_doc()
 * @used-by df_assert_oq()
 * @used-by df_currency_base()
 * @used-by df_is_sales_doc()
 * @used-by df_oq()
 * @used-by dfp()
 * @used-by dfpex_args()
 * @used-by dfpm()
 * @param mixed $v
 */
function df_is_oq($v):bool {return df_is_o($v) || df_is_q($v);}