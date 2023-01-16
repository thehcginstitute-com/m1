<?php
 /**
 * @category   Webkul
 * @package    Webkul_CustomField
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */ 
class Webkul_CustomField_Block_Adminhtml_Customfields extends Mage_Adminhtml_Block_Widget_Grid_Container
{	
	/**
	*Constructor. sets Header text and label for Grid
	*
	**/
	public function __construct() {
	        $this->_controller = "adminhtml_customfields";
	        $this->_blockGroup = "customfield";
	        $this->_headerText = $this->__("Custom Registration Fields");
	        $this->_addButtonLabel = $this->__("Add Field");
	        parent::__construct();
	    }
}