<?php 
 /**
 * @category   Webkul
 * @package    Webkul_CustomField
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */ 
class Webkul_CustomField_Block_Adminhtml_Customfields_Grid_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
		/**
		*@param Row object
		*@return string
		**/
    public function render(Varien_Object $row) {
    	$rowData=$row->getData();
		if ($rowData['backend_type']=='int' && $rowData['dependable_inputname']!="")
			return 'dependable';
    	elseif($rowData['backend_type']=='int')
    		return 'boolean';
    	else    		
			return $rowData['frontend_input'];
		
    }
}