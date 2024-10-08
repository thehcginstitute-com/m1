<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml media library image editor
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Media_Editor extends Mage_Adminhtml_Block_Widget
{
    /**
     * @var Varien_Object|null
     */
    protected $_config;

    /**
     * Mage_Adminhtml_Block_Media_Editor constructor.
     */
    function __construct()
    {
        parent::__construct();
        $this->setTemplate('media/editor.phtml');
        $this->getConfig()->setImage($this->getSkinUrl('images/image.jpg'));
        $this->getConfig()->setParams();
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'rotatecw_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData([
                    'id'      => $this->_getButtonId('rotatecw'),
                    'label'   => Mage::helper('adminhtml')->__('Rotate CW'),
                    'onclick' => $this->getJsObjectName() . '.rotateCw()'
                ])
        );

        $this->setChild(
            'rotateccw_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData([
                    'id'      => $this->_getButtonId('rotateccw'),
                    'label'   => Mage::helper('adminhtml')->__('Rotate CCW'),
                    'onclick' => $this->getJsObjectName() . '.rotateCCw()'
                ])
        );

        $this->setChild(
            'resize_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData([
                    'id'      => $this->_getButtonId('upload'),
                    'label'   => Mage::helper('adminhtml')->__('Resize'),
                    'onclick' => $this->getJsObjectName() . '.resize()'
                ])
        );

        $this->setChild(
            'image_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData([
                    'id'      => $this->_getButtonId('image'),
                    'label'   => Mage::helper('adminhtml')->__('Get Image Base64'),
                    'onclick' => $this->getJsObjectName() . '.getImage()'
                ])
        );

        return parent::_prepareLayout();
    }

    /**
     * @param string $buttonName
     * @return string
     */
    protected function _getButtonId($buttonName)
    {
        return $this->getHtmlId() . '-' . $buttonName;
    }

    /**
     * @return string
     */
    function getRotatecwButtonHtml()
    {
        return $this->getChildHtml('rotatecw_button');
    }

    /**
     * @return string
     */
    function getImageButtonHtml()
    {
        return $this->getChildHtml('image_button');
    }

    /**
     * @return string
     */
    function getRotateccwButtonHtml()
    {
        return $this->getChildHtml('rotateccw_button');
    }

    /**
     * @return string
     */
    function getResizeButtonHtml()
    {
        return $this->getChildHtml('resize_button');
    }

    /**
     * Retrieve uploader js object name
     *
     * @return string
     */
    function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * Retrieve config json
     *
     * @return string
     */
    function getConfigJson()
    {
        return Mage::helper('core')->jsonEncode($this->getConfig()->getData());
    }

    /**
     * Retrieve config object
     *
     * @return Varien_Object
     */
    function getConfig()
    {
        if (is_null($this->_config)) {
            $this->_config = new Varien_Object();
        }

        return $this->_config;
    }
}
