<?php
use Mage_Eav_Model_Entity_Attribute_Abstract as A;
use Mage_Eav_Model_Entity_Attribute_Interface as IA;
/**
 * 2016-06-04
 * 2024-05-15
 * 1) "Port `df_customer_att()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/609
 * 2.1) "Improve `df_customer_att()`": https://github.com/mage2pro/core/issues/371
 * 2.2) `print_r([2 => 'numeric', '2' => 'literal']);` => «Array ([2] => literal)»: https://3v4l.org/jatMt
 * @used-by df_customer_att_is_required()
 * @used-by \HCG\MailChimp\Tags::_p() (https://github.com/thehcginstitute-com/m1/issues/589)
 * @param string|int|IA $a
 * @return IA|A
 */
function df_customer_att($a):IA {return df_eav_config()->getAttribute(df_eav_customer(), $a);}