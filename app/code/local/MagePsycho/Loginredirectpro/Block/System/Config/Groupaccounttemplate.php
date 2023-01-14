<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Loginredirectpro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Loginredirectpro_Block_System_Config_Groupaccounttemplate extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	/**
	 * @var MagePsycho_Loginredirectpro_Block_Adminhtml_System_Config_Form_Field_Customergroup
	 */
	protected $_groupRenderer;

	/**
	 * @var MagePsycho_Loginredirectpro_Block_Adminhtml_System_Config_Form_Field_Emailtemplate
	 */
	protected $_emailTemplateRenderer;

	/**
	 * Retrieve group column renderer
	 *
	 * @return MagePsycho_Loginredirectpro_Block_Adminhtml_System_Config_Form_Field_Customergroup
	 */
	protected function _getGroupRenderer()
	{
		if ( ! $this->_groupRenderer) {
			$this->_groupRenderer = $this->getLayout()->createBlock(
					'magepsycho_loginredirectpro/adminhtml_system_config_form_field_customergroup', '',
					array('is_render_to_js_template' => true)
			);
			$this->_groupRenderer->setClass('customer_group_select');
			$this->_groupRenderer->setExtraParams('style="width:86%"');
		}
		return $this->_groupRenderer;
	}

	/**
	 * Retrieve email template column renderer
	 *
	 * @return MagePsycho_Loginredirectpro_Block_Adminhtml_System_Config_Form_Field_Emailtemplate
	 */
	protected function _getEmailTemplateRenderer()
	{
		if ( !$this->_emailTemplateRenderer) {
			$this->_emailTemplateRenderer = $this->getLayout()->createBlock(
					'magepsycho_loginredirectpro/adminhtml_system_config_form_field_emailtemplate', '',
					array('is_render_to_js_template' => true)
			);
			$this->_emailTemplateRenderer->setClass('email_template_select');
			//$this->_groupRenderer->setExtraParams('style="width:86%"');
		}
		return $this->_emailTemplateRenderer;
	}

	/**
	 * Prepare to render
	 */
	protected function _prepareToRender()
	{
		$this->addColumn('customer_group_id', array(
				'label' => Mage::helper('customer')->__('Customer Group'),
				'renderer' => $this->_getGroupRenderer(),
		));
		$this->addColumn('account_template', array(
				'label' => Mage::helper('magepsycho_loginredirectpro')->__('New Account Email Template'),
				'renderer' => $this->_getEmailTemplateRenderer(),
		));
		$this->_addAfter = false;
		$this->_addButtonLabel = Mage::helper('magepsycho_loginredirectpro')->__('Add Email Template');
	}

	/**
	 * Prepare existing row data object
	 *
	 * @param Varien_Object
	 */
	protected function _prepareArrayRow(Varien_Object $row)
	{
		$row->setData(
				'option_extra_attr_' . $this->_getGroupRenderer()->calcOptionHash($row->getData('customer_group_id')),
				'selected="selected"'
		);
		$row->setData(
				'option_extra_attr_' . $this->_getEmailTemplateRenderer()->calcOptionHash($row->getData('account_template')),
				'selected="selected"'
		);
	}
}