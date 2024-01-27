<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Loginredirectpro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Loginredirectpro_Block_Adminhtml_System_Config_Form_Field_Emailtemplate extends Mage_Core_Block_Html_Select
{
	/**
	 * Email Templates cache
	 *
	 * @var array
	 */
	private $_emailTemplates;

	/**
	 * Retrieve allowed email templates
	 *
	 * @param int $templateId return name by email id
	 * @return array|string
	 */
	protected function _getEmailTemplates($templateId = null)
	{
		if (is_null($this->_emailTemplates)) {
			$this->_emailTemplates = array();

			$options = Mage::getSingleton('adminhtml/system_config_source_email_template')
			               ->toOptionArray();
			foreach ($options as $option) {
				$this->_emailTemplates[$option['value']] = $option['label'];
			}
		}
		if ( !is_null($templateId)) {
			return isset($this->_emailTemplates[$templateId]) ? $this->_emailTemplates[$templateId] : null;
		}
		return $this->_emailTemplates;
	}

	function setInputName($value)
	{
		return $this->setName($value);
	}

	/**
	 * Render block HTML
	 *
	 * @return string
	 */
	function _toHtml()
	{
		if ( !$this->getOptions()) {
			foreach ($this->_getEmailTemplates() as $id => $label) {
				$this->addOption($id, addslashes($label));
			}
		}
		return parent::_toHtml();
	}
}
