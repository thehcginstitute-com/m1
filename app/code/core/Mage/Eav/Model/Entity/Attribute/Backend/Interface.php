<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Entity attribute backend interface
 *
 * Backend is responsible for saving the values of the attribute
 * and performing pre and post actions
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Eav_Model_Entity_Attribute_Backend_Interface
{
    function getTable();
    function isStatic();
    function getType();
    function getEntityIdField();

    /**
     * @param int $valueId
     * @return int
     */
    function setValueId($valueId);
    function getValueId();

    /**
     * @param object $object
     * @return mixed
     */
    function afterLoad($object);

    /**
     * @param object $object
     * @return mixed
     */
    function beforeSave($object);

    /**
     * @param object $object
     * @return mixed
     */
    function afterSave($object);

    /**
     * @param object $object
     * @return mixed
     */
    function beforeDelete($object);

    /**
     * @param object $object
     * @return mixed
     */
    function afterDelete($object);

    /**
     * Get entity value id
     *
     * @param Varien_Object $entity
     */
    function getEntityValueId($entity);

    /**
     * Set entity value id
     *
     * @param Varien_Object $entity
     * @param int $valueId
     */
    function setEntityValueId($entity, $valueId);
}
