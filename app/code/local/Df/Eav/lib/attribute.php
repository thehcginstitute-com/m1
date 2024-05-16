<?php
use Mage_Core_Model_Abstract as M;
use Mage_Eav_Model_Entity_Attribute_Abstract as A;
/**
 * 2024-05-16
 * 1) "Implement `df_att_val_s()`": https://github.com/mage2pro/core/issues/373
 * 2) "Port `df_att_val()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/619
 * @uses Mage_Eav_Model_Entity_Attribute_Source_Abstract::getOptionText()
 * @used-by df_product_att_val()
 */
function df_att_val(M $m, A $a, string $d = ''):string {return df_fnes($r = $m[$a->getAttributeCode()]) ? $d : (
	!$a->usesSource() ? $r : (
		/**
		 * 2020-01-31
		 * @see \Magento\Eav\Model\Entity\Attribute\Source\Table::getOptionText() can return an empty array
		 * for an attribute's value (e.g., for the `description` attribute), if the value contains a comma.
		 */
		is_array($r = $a->getSource()->getOptionText($prev = $r)) ? $prev : (
			df_fnes($r) ? $d : $r
		)
	)
);}