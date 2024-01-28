<?php

/**
 * Block to render customer's group_id attribute
 *
 * @category   MagePsycho
 * @package    MagePsycho_Customerregfields
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Customerregfields_Block_Customer_Widget_Type_Groupid extends MagePsycho_Customerregfields_Block_Customer_Widget_Abstract
{
    const ATTRIBUTE_GROUP_ID = 'group_id';

    function _construct()
    {
        parent::_construct();
        $this->setTemplate('magepsycho/customerregfields/customer/widget/type/group_id.phtml');
    }

    function isEnabled()
    {
        return (bool)$this->_getAttribute(self::ATTRIBUTE_GROUP_ID)->getIsVisible();
    }

    function isRequired()
    {
        return (bool)$this->_getAttribute(self::ATTRIBUTE_GROUP_ID)->getIsRequired();
    }

    function getGroupSelectOptions()
    {
        return hcg_mp_hc()->getGroupSelectOptions();
    }

    function getGroupSelectHtml($name, $selectedValue = '', $class = '')
    {
        $groupOptions   = $this->getGroupSelectOptions();
        $fieldName      = $this->getFieldName($name);
        $fieldId        = $this->getFieldId($name);
        $select         = $this->getLayout()->createBlock('core/html_select');
        return $select->setAttribute('name', $fieldName)
                      ->setId($fieldId)
                      ->setClass($class)
                      ->setOptions($groupOptions)
                      ->setValue($selectedValue)
                      //->setExtraParams($extraParams)
                      ->toHtml();
    }

}
