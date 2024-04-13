<?php
# 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
class Ebizmarts_MailChimp_Adminhtml_MergevarsController extends Mage_Adminhtml_Controller_Action
{

	function addmergevarAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	function saveaddAction()
	{
		$postData = $this->getRequest()->getPost('mergevar', array());
		$label = $postData['label'];
		$value = $postData['value'];
		$fieldType = $postData['fieldtype'];
		$helper = $this->makeHelper();
		$scopeArray = $helper->getCurrentScope();
		$blankSpacesAmount = (count(explode(' ', $value)) - 1);

		if (is_numeric($value)) {
			Mage::getSingleton('adminhtml/session')
				->addError(
					$this->__(
						'There was an error processing the new field. '
						. 'MailChimp tag value can not be numeric.'
					)
				);
		} elseif ($helper->customMergeFieldAlreadyExists($value, $scopeArray['scope_id'], $scopeArray['scope'])) {
			Mage::getSingleton('adminhtml/session')
				->addError(
					$this->__(
						'There was an error processing the new field. '
						. 'MailChimp tag value already exists.'
					)
				);
		} elseif ($blankSpacesAmount > 0) {
			Mage::getSingleton('adminhtml/session')
				->addError(
					$this->__(
						'There was an error processing the new field. '
						. 'MailChimp tag value can not contain blank spaces.'
					)
				);
		} else {
			$customMergeFields = $helper->getCustomMergeFields($scopeArray['scope_id'], $scopeArray['scope']);
			$customMergeFields[] = array('label' => $label, 'value' => $value, 'field_type' => $fieldType);
			hcg_mc_cfg_save(
				Ebizmarts_MailChimp_Model_Config::GENERAL_CUSTOM_MAP_FIELDS
				,$helper->serialize($customMergeFields)
				,$scopeArray['scope_id']
				,$scopeArray['scope']
			);
			Mage::getSingleton('core/session')->setMailChimpValue($value);
			Mage::getSingleton('core/session')->setMailChimpLabel($label);
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The custom value was added successfully.'));
		}

		$this->_redirect("*/*/addmergevar");
	}

	protected function _isAllowed()
	{
		switch ($this->getRequest()->getActionName()) {
		case 'addmergevar':
		case 'saveadd':
			$acl = 'system/config/mailchimp';
			break;
		}

		return Mage::getSingleton('admin/session')->isAllowed($acl);
	}

	/**
	 * @return Ebizmarts_MailChimp_Helper_Data
	 */
	protected function makeHelper()
	{
		return Mage::helper('mailchimp');
	}
}
