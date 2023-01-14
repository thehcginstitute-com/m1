<?php

/**
 * Block to render customer's group code attribute
 *
 * @category   MagePsycho
 * @package    MagePsycho_Customerregfields
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Customerregfields_Block_Customer_Widget_Type_Groupcode extends MagePsycho_Customerregfields_Block_Customer_Widget_Abstract
{
    const ATTRIBUTE_GROUP_CODE = 'mp_group_code';

    /**
     * Initialize block
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('magepsycho/customerregfields/customer/widget/type/group_code.phtml');
    }

    /**
     * Check if mp_group_code attribute enabled in system
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->_getAttribute(self::ATTRIBUTE_GROUP_CODE)->getIsVisible();
    }

}
