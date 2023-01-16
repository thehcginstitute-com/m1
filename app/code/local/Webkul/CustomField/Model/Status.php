<?php
 /**
 * @category   Webkul
 * @package    Webkul_CustomField
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */ 
class Webkul_CustomField_Model_Status extends Varien_Object {
	/**
	*constant variable used to set status field value
	*/
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    /**
    *@return array 
    */
    static public function getOptionArray() {
        return array(
            self::STATUS_ENABLED => Mage::helper("customfield")->__("Enabled"),
            self::STATUS_DISABLED => Mage::helper("customfield")->__("Disabled")
        );
    }

}

