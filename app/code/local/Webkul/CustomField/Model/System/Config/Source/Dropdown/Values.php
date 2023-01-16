<?php
 /**
 * @category   Webkul
 * @package    Webkul_CustomField
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */ 
class Webkul_Customfield_Model_System_Config_Source_Dropdown_Values
{
    /**
    *@return array
    */
   	public function toOptionArray(){
   		
		 return array(
            array(
                'value' => 'is_html',
                'label' => 'HTML',
            ),
            array(
                'value' => 'is_text',
                'label' => 'Text',
            ),
        );
    }
}