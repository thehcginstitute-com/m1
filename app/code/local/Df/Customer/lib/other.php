<?php
use Mage_Customer_Model_Customer as C;

/**
 * 2016-12-04
 * 2024-03-17 "Port `df_customer_id()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/498
 * 2024-06-02 "Improve `df_customer_id()`": https://github.com/thehcginstitute-com/m1/issues/634
 * @used-by df_context()
 * @used-by df_customer_is_need_confirm()
 * @used-by vendor/inkifi/mediaclip-legacy/view/frontend/templates/savedproject.phtml
 * @param C|int|null $v [optional]
 * @return int|null
 */
function df_customer_id($v = null) {return !$v && !df_is_backend() ? df_customer_session()->getId() : (
	$v instanceof C ? $v->getId() : $v
);}

/**
 * 2024-03-03
 * "Implement the `df_customer_is_anon()` function": https://github.com/thehcginstitute-com/m1/issues/447
 * @used-by app/design/frontend/default/mobileshoppe/template/catalog/category/view.phtml()
 */
function df_customer_is_anon():bool {return !df_customer_group_id();}