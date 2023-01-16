<?php
 /**
 * @category   Webkul
 * @package    Webkul_CustomField
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */ 
class Webkul_CustomField_Block_Customfield extends Mage_Core_Block_Template	{
	/*
	*Constructor. set the customer fields collection
	*
	*/
	public function __construct(){
		parent::__construct();
		$model = 'customer/attribute_collection'; 
		$type='customer'; 
		$collection = Mage::getResourceModel($model)  
						->setEntityTypeFilter( Mage::getModel('eav/entity')->setType($type)->getTypeId() )  
						->addVisibleFilter()
						->addFilter('is_user_defined', 1)->setOrder('sort_order', 'ASC'); 

		$this->setCollection($collection);
	}

	/**
	 * Add's link to the customer account navigation
	 *
	 * @return string
	 */
    public function addLinkToParentBlock() 
    {
        $parent = $this->getParentBlock();
        $additionalinfo = Mage::getModel("customfield/customfields")->getCollection()->addFieldToFilter("status",1)->setOrder("sort_order","ASC");
        if ($parent) {
            if (count($additionalinfo)) {
                $parent->addLink(
                    'Account Additional Info',
                    'customfield/index/customfield',
                    'Account Additional Info'
                );

            }
        } 
    }



}

