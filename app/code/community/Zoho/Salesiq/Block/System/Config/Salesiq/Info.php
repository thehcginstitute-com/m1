<?php
/**
 * @category   Zoho
 * @package    Zoho_Salesiq
 * @author     SalesIQ Team
 * @website    http://www.zoho.com/salesiq
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Zoho_Salesiq_Block_System_Config_Salesiq_Info
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface {

    public function render(Varien_Data_Form_Element_Abstract $element) {
        $html = '<div style="border:1px solid #CCCCCC;margin-bottom:10px;padding:10px 10px 10px 10px;">
            <h4>About Zoho SalesIQ</h4>
            <p>Engage with your customers in real time. Chat and deliver unbeatable customer support. Convert more, grow faster.</p>
          </div>';
        return $html;
    }
}
