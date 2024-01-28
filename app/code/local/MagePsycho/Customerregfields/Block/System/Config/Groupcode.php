<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Customerregfields
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Customerregfields_Block_System_Config_Groupcode extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	/**
	 * @var MagePsycho_Customerregfields_Block_Adminhtml_System_Config_Form_Field_Customergroup
	 */
	protected $_groupRenderer;

	/**
	 * Retrieve group column renderer
	 *
	 * @return MagePsycho_Customerregfields_Block_Adminhtml_System_Config_Form_Field_Customergroup
	 */
	protected function _getGroupRenderer()
	{
		if ( ! $this->_groupRenderer) {
			$this->_groupRenderer = $this->getLayout()->createBlock(
					'magepsycho_customerregfields/adminhtml_system_config_form_field_customergroup', '',
					array('is_render_to_js_template' => true)
			);
			$this->_groupRenderer->setClass('customer_group_select');
			$this->_groupRenderer->setExtraParams('style="width:86%"');
		}
		return $this->_groupRenderer;
	}

	/**
	 * Prepare to render
	 */
	protected function _prepareToRender()
	{
		$this->addColumn('customer_group_id', array(
			'label' => hcg_mp_hc()->__('Customer Group'),
			'renderer' => $this->_getGroupRenderer(),
		));
		$this->addColumn('group_code', array(
			'label' => hcg_mp_hc()->__('Code'),
			'style' => 'width:100px',
		));
		$this->_addAfter = false;
		$this->_addButtonLabel = hcg_mp_hc()->__('Add Group Code');
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
	}
}