<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract message model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Core_Model_Message_Abstract
{
    protected $_type;
    protected $_code;
    protected $_class;
    protected $_method;
    protected $_identifier;
    protected $_isSticky = false;

    /**
     * Mage_Core_Model_Message_Abstract constructor.
     * @param string $type
     * @param string $code
     */
    function __construct($type, $code = '')
    {
        $this->_type = $type;
        $this->_code = $code;
    }

    /**
     * @return string
     */
    function getCode()
    {
        return $this->_code;
    }

    /**
     * @return string
     */
    function getText()
    {
        return $this->getCode();
    }

    /**
     * @return string
     */
    function getType()
    {
        return $this->_type;
    }

    /**
     * @param string $class
     * @return $this
     */
    function setClass($class)
    {
        $this->_class = $class;
        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    function setMethod($method)
    {
        $this->_method = $method;
        return $this;
    }

    /**
     * @return string
     */
    function toString()
    {
        return $this->getType() . ': ' . $this->getText();
    }

    /**
     * Set message identifier
     *
     * @param string $id
     * @return Mage_Core_Model_Message_Abstract
     */
    function setIdentifier($id)
    {
        $this->_identifier = $id;
        return $this;
    }

    /**
     * Get message identifier
     *
     *  @return string
     */
    function getIdentifier()
    {
        return $this->_identifier;
    }

    /**
     * Set message sticky status
     *
     * @param bool $isSticky
     * @return Mage_Core_Model_Message_Abstract
     */
    function setIsSticky($isSticky = true)
    {
        $this->_isSticky = $isSticky;
        return $this;
    }

    /**
     * Get whether message is sticky
     *
     * @return bool
     */
    function getIsSticky()
    {
        return $this->_isSticky;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Mage_Core_Model_Message_Abstract
     */
    function setCode($code)
    {
        $this->_code = $code;
        return $this;
    }
}
