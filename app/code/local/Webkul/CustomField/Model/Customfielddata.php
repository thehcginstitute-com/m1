<?php
 /**
 * @category   Webkul
 * @package    Webkul_CustomField
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */ 
class Webkul_CustomField_Model_Customfielddata extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init("customfield/customfielddata");
    }

}

