<?php
use Df\Core\Exception as DFE;
use Throwable as Th; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2020-01-21
 * 2024-03-16 "Port `df_error` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/487
 * @used-by df_assert()
 * @used-by Mage_Eav_Model_Attribute_Data_Abstract::_applyInputFilter()
 * @used-by Varien_Data_Form_Filter_Date::inputFilter()
 * @param string|mixed|Th|null ...$a
 * @throws DFE
 */
function df_error(...$a) {throw df_error_create(...$a);}

/**
 * 2016-07-31
 * 2024-03-16 "Port `df_error_create` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/488
 * @used-by df_error()
 * @used-by df_error_html()
 * @param string|array(string|Th)|mixed|Th|null ...$a
 */
function df_error_create(...$a):DFE {return df_is_th($a0 = dfa($a, 0)) ? DFE::wrap($a0) : new DFE(...$a);}