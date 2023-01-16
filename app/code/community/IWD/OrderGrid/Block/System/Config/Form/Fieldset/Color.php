<?php

/**
 * Class IWD_OrderGrid_Block_System_Config_Form_Fieldset_Color
 */
class IWD_OrderGrid_Block_System_Config_Form_Fieldset_Color extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Color statuses setting
     */
    const XML_PATH_ORDER_GRID_STATUS_COLOR = 'iwd_ordermanager/grid_order/status_color';

    /**
     * @var string
     */
    protected $_statusColorElement = "";

    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->prependColorElement();
        $this->addItemsToColorElement();
        $this->appendColorElement();

        return $this->_statusColorElement;
    }

    protected function addItemsToColorElement()
    {
        $statuses = Mage::getSingleton('sales/order_config')->getStatuses();

        foreach ($statuses as $code => $label) {
            $this->addListItemToColorElement($code, $label);
        }
    }

    /**
     * @param $code
     * @param $label
     */
    protected function addListItemToColorElement($code, $label)
    {
        $clearButton = $this->getClearColorButton();
        $this->_statusColorElement .= '<li id="' . $code . '">'
            . '<span class="color-box">' . $label . '</span>'
            . $this->getClearColorButton()
            . '</li>';
    }

    /**
     * @return string
     */
    protected function getClearColorButton()
    {
        $clearText = Mage::helper('iwd_ordergrid')->__('Clear color');
        return '<span class="clear-color" title="' . $clearText . '">X<span>';
    }

    protected function prependColorElement()
    {
        $this->_statusColorElement .= '<ul id="order_status_color">';
    }

    protected function appendColorElement()
    {
        $this->_statusColorElement .= '</ul><input type="hidden" id="iwd_ordergrid_grid_order_status_color"
        name="groups[grid_order][fields][status_color][value]" value="' . $this->getStatusColor() . '" />';
    }

    protected function getStatusColor()
    {
        return Mage::getStoreConfig(self::XML_PATH_ORDER_GRID_STATUS_COLOR);
    }
}