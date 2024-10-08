<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_SalesRule_Model_Rule_Condition_Product_Found
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method setValueOption(array $array)
 */
class Mage_SalesRule_Model_Rule_Condition_Product_Found extends Mage_SalesRule_Model_Rule_Condition_Product_Combine
{
    function __construct()
    {
        parent::__construct();
        $this->setType('salesrule/rule_condition_product_found');
    }

    /**
     * Load value options
     *
     * @return $this
     */
    function loadValueOptions()
    {
        $this->setValueOption([
            1 => Mage::helper('salesrule')->__('FOUND'),
            0 => Mage::helper('salesrule')->__('NOT FOUND')
        ]);
        return $this;
    }

    /**
     * @return string
     */
    function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . Mage::helper('salesrule')->__("If an item is %s in the cart with %s of these conditions true:", $this->getValueElement()->getHtml(), $this->getAggregatorElement()->getHtml());
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    /**
     * validate
     *
     * @param Varien_Object $object Quote
     * @return bool
     */
    function validate(Varien_Object $object)
    {
        $all = $this->getAggregator() === 'all';
        $true = (bool)$this->getValue();
        $found = false;
        foreach ($object->getAllItems() as $item) {
            $found = $all;
            foreach ($this->getConditions() as $cond) {
                $validated = $cond->validate($item);
                if (($all && !$validated) || (!$all && $validated)) {
                    $found = $validated;
                    break;
                }
            }
            if (($found && $true) || (!$true && $found)) {
                break;
            }
        }
        // found an item and we're looking for existing one
        if ($found && $true) {
            return true;
        } elseif (!$found && !$true) { // not found and we're making sure it doesn't exist
            return true;
        }
        return false;
    }
}
