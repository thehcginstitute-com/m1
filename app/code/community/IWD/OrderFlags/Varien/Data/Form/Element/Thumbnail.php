<?php

/**
 * Class IWD_OrderFlags_Varien_Data_Form_Element_Thumbnail
 */
class IWD_OrderFlags_Varien_Data_Form_Element_Thumbnail extends Varien_Data_Form_Element_Abstract
{
    /**
     * IWD_OrderFlags_Varien_Data_Form_Element_Thumbnail constructor.
     * @param array $data
     */
    function __construct($data)
    {
        parent::__construct($data);
        $this->setType('file');
    }

    /**
     * @return string
     */
    function getElementHtml()
    {
        $html = '';

        if ($this->getValue()) {
            $html = '<img id="' . $this->getHtmlId() . '_image" title="' . $this->getValue() . '"'
                    . 'class="margin-left" style="display:block;margin-bottom:15px;height:34px; max-width:90px;"'
                    . 'src="' . $this->getValue() . '" alt="' . $this->getValue() . '">';
        }

        $this->setClass('input-file margin-left');
        if (!$this->getValue() && $this->getRequired()) {
            $this->addClass('required-entry');
        }

        return $html . parent::getElementHtml();
    }

    /**
     * @return mixed
     */
    protected function _getUrl()
    {
        return $this->getValue();
    }

    /**
     * @return mixed
     */
    function getName()
    {
        return $this->getData('name');
    }
}
