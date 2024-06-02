<?php
use Mage_Customer_Model_Customer as C;
use Mage_Newsletter_Model_Subscriber as Sub;

/**
 * 2016-12-04
 * 2024-03-17 "Port `df_customer_id()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/498
 * 2024-06-02 "Improve `df_customer_id()`": https://github.com/thehcginstitute-com/m1/issues/634
 * @used-by df_context()
 * @used-by df_customer_is_need_confirm()
 * @used-by vendor/inkifi/mediaclip-legacy/view/frontend/templates/savedproject.phtml
 * @param string|int|C|Sub|null $v [optional]
 * @param Closure|bool|mixed $onE [optional]
 */
function df_customer_id($v = null, $onE = null):?int {return df_try(function() use($v, $onE):?int {return
	df_customer($v, $onE)->getId()
;}, $onE);}