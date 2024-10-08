<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert component collection
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Convert_Container_Collection
{
    protected $_items = [];

    protected $_defaultClass = 'Mage_Dataflow_Model_Convert_Container_Generic';

    function setDefaultClass($className)
    {
        $this->_defaultClass = $className;
        return $this;
    }

    function addItem($name, Mage_Dataflow_Model_Convert_Container_Interface $item)
    {
        if (is_null($name)) {
            if ($item->getName()) {
                $name = $item->getName();
            } else {
                $name = count($this->_items);
            }
        }

        $this->_items[$name] = $item;

        return $item;
    }

    function getItem($name)
    {
        if (!isset($this->_items[$name])) {
            $this->addItem($name, new $this->_defaultClass());
        }
        return $this->_items[$name];
    }

    function hasItem($name)
    {
        return isset($this->_items[$name]);
    }
}
