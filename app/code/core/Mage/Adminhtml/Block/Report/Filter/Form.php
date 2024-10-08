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
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml report filter form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Filter_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Report type options
	 */
	protected $_reportTypeOptions = [];

	/**
	 * Report field visibility
	 */
	protected $_fieldVisibility = [];

	/**
	 * Report field opions
	 */
	protected $_fieldOptions = [];

	/**
     * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 */
	final function setInvisible(string $field) {$this->_fieldVisibility[$field] = false;}

	/**
	 * Get field visibility
	 *
	 * @param string $fieldId Field id
	 * @param bool $defaultVisibility Default field visibility
	 * @return bool
	 */
	function getFieldVisibility($fieldId, $defaultVisibility = true)
	{
		if (!array_key_exists($fieldId, $this->_fieldVisibility)) {
			return $defaultVisibility;
		}
		return $this->_fieldVisibility[$fieldId];
	}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 */
	final function setFieldOption(string $f, string $k, string $v):void {
		if (!array_key_exists($f, $this->_fieldOptions)) {
			$this->_fieldOptions[$f] = [];
		}
		$this->_fieldOptions[$f][$k] = $v;
	}

	/**
	 * Add report type option
	 * 2024-09-13 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--8/app/design/adminhtml/default/default/layout/sales.xml#L1159
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--8/app/design/adminhtml/default/default/layout/sales.xml#L1160
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--8/app/design/adminhtml/default/default/layout/sales.xml#L1178
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--8/app/design/adminhtml/default/default/layout/sales.xml#L1179
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--8/app/design/adminhtml/default/default/layout/sales.xml#L1201
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--8/app/design/adminhtml/default/default/layout/sales.xml#L1202
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--8/app/design/adminhtml/default/default/layout/sales.xml#L1219
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--8/app/design/adminhtml/default/default/layout/sales.xml#L1220
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--8/app/design/adminhtml/default/default/layout/sales.xml#L1237
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--8/app/design/adminhtml/default/default/layout/sales.xml#L1238
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--8/app/design/adminhtml/default/default/layout/sales.xml#L1255
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--8/app/design/adminhtml/default/default/layout/sales.xml#L1256
	 */
	final function addReportTypeOption(string $k, string $v):void {$this->_reportTypeOptions[$k] = $v;}

	/**
	 * Add fieldset with general report fields
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		$actionUrl = $this->getUrl('*/*/sales');
		$form = new Varien_Data_Form(
			['id' => 'filter_form', 'action' => $actionUrl, 'method' => 'get']
		);
		$htmlIdPrefix = 'sales_report_';
		$form->setHtmlIdPrefix($htmlIdPrefix);
		$fieldset = $form->addFieldset('base_fieldset', ['legend' => Mage::helper('reports')->__('Filter')]);

		$dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

		$fieldset->addField('store_ids', 'hidden', [
			'name'  => 'store_ids'
		]);

		$fieldset->addField('report_type', 'select', [
			'name'      => 'report_type',
			'options'   => $this->_reportTypeOptions,
			'label'     => Mage::helper('reports')->__('Match Period To'),
		]);

		$fieldset->addField('period_type', 'select', [
			'name' => 'period_type',
			'options' => [
				'day'   => Mage::helper('reports')->__('Day'),
				'month' => Mage::helper('reports')->__('Month'),
				'year'  => Mage::helper('reports')->__('Year')
			],
			'label' => Mage::helper('reports')->__('Period'),
			'title' => Mage::helper('reports')->__('Period')
		]);

		$fieldset->addField('from', 'date', [
			'name'      => 'from',
			'format'    => $dateFormatIso,
			'image'     => $this->getSkinUrl('images/grid-cal.gif'),
			'label'     => Mage::helper('reports')->__('From'),
			'title'     => Mage::helper('reports')->__('From'),
			'required'  => true
		]);

		$fieldset->addField('to', 'date', [
			'name'      => 'to',
			'format'    => $dateFormatIso,
			'image'     => $this->getSkinUrl('images/grid-cal.gif'),
			'label'     => Mage::helper('reports')->__('To'),
			'title'     => Mage::helper('reports')->__('To'),
			'required'  => true
		]);

		$fieldset->addField('show_empty_rows', 'select', [
			'name'      => 'show_empty_rows',
			'options'   => [
				'1' => Mage::helper('reports')->__('Yes'),
				'0' => Mage::helper('reports')->__('No')
			],
			'label'     => Mage::helper('reports')->__('Empty Rows'),
			'title'     => Mage::helper('reports')->__('Empty Rows')
		]);

		$form->setUseContainer(true);
		$this->setForm($form);

		return parent::_prepareForm();
	}

	/**
	 * Initialize form fileds values
	 * Method will be called after prepareForm and can be used for field values initialization
	 *
	 * @return Mage_Adminhtml_Block_Widget_Form
	 */
	protected function _initFormValues()
	{
		$data = $this->getFilterData()->getData();
		foreach ($data as $key => $value) {
			if (is_array($value) && isset($value[0])) {
				$data[$key] = explode(',', $value[0]);
			}
		}
		$this->getForm()->addValues($data);
		return parent::_initFormValues();
	}

	/**
	 * This method is called before rendering HTML
	 *
	 * @return Mage_Adminhtml_Block_Widget_Form
	 */
	protected function _beforeToHtml()
	{
		$result = parent::_beforeToHtml();

		/** @var Varien_Data_Form_Element_Fieldset $fieldset */
		$fieldset = $this->getForm()->getElement('base_fieldset');

		if (is_object($fieldset) && $fieldset instanceof Varien_Data_Form_Element_Fieldset) {
			// apply field visibility
			foreach ($fieldset->getElements() as $field) {
				if (!$this->getFieldVisibility($field->getId())) {
					$fieldset->removeField($field->getId());
				}
			}
			// apply field options
			foreach ($this->_fieldOptions as $fieldId => $fieldOptions) {
				$field = $fieldset->getElements()->searchById($fieldId);
				/** @var Varien_Object $field */
				if ($field) {
					foreach ($fieldOptions as $k => $v) {
						$field->setDataUsingMethod($k, $v);
					}
				}
			}
		}

		return $result;
	}
}
