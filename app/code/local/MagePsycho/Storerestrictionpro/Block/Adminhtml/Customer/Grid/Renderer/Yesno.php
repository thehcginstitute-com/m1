<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Storerestrictionpro
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Storerestrictionpro_Block_Adminhtml_Customer_Grid_Renderer_Yesno extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    function render(Varien_Object $row)
    {
        $helper             = Mage::helper('magepsycho_storerestrictionpro');
        $isAccountActivated = ((bool)$row->getAccountActivated()) ? 'Yes' : 'No';
        return $helper->__($isAccountActivated);
    }
}