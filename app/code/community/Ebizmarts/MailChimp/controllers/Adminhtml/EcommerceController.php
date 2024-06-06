<?php

/**
 * mc-magento Magento Component
 *
 * @category  Ebizmarts
 * @package   mc-magento
 * @author    Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date:     5/27/16 1:50 PM
 * @file:     EcommerceController.php
 */
class Ebizmarts_MailChimp_Adminhtml_EcommerceController extends Mage_Adminhtml_Controller_Action
{
	function renderresendecomAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	function resetLocalErrorsAction() {
		$helper = $this->makeHelper();
		$mageApp = $helper->getMageApp();
		$request = $mageApp->getRequest();
		$scope = $request->getParam('scope');
		$scopeId = $request->getParam('scope_id');
		$success = 1;
		try {
			$stores = $mageApp->getStores();
			if ($scopeId == 0) {
				foreach ($stores as $store) {
					$helper->resetErrors($store->getId());
				}
			}
			$helper->resetErrors($scopeId, $scope);
		}
		catch (Exception $e) {
			df_log($e);
			$success = 0;
		}
		$mageApp->getResponse()->setBody($success);
	}

	function resendEcommerceDataAction()
	{
		$helper = $this->makeHelper();
		$mageApp = $helper->getMageApp();
		$request = $this->getRequest();
		$filters = $request->getParam('filter');
		$scope = $request->getParam('scope');
		$scopeId = $request->getParam('scope_id');
		$success = 0;

		if (is_array($filters) && empty($filters)) {
			$this->addWarning($helper->__('At least one type of eCommerce data should be selected to Resend.'));
			$success = $helper->__('Redirecting... ')
				. '<script type="text/javascript">window.top.location.reload();</script>';
		} else {
			try {
				$helper->resendMCEcommerceData($scopeId, $scope, $filters);

				$this->addSuccess($helper->__('Ecommerce data resent succesfully'));
				$success = $helper->__('Redirecting... ')
					. '<script type="text/javascript">window.top.location.reload();</script>';
			}
			catch (Exception $e) {
				df_log($e);
				$this->addError($e->getMessage());
			}
		}

		$mageApp->getResponse()->setBody($success);
	}

	function createMergeFieldsAction()
	{
		$helper = $this->makeHelper();
		$mageApp = $helper->getMageApp();
		$request = $mageApp->getRequest();
		$scope = $request->getParam('scope');
		$scopeId = $request->getParam('scope_id');
		$success = 0;
		$subEnabled = $helper->isSubscriptionEnabled($scopeId, $scope);

		if ($subEnabled) {
			$success = $helper->createMergeFields($scopeId, $scope);
		}

		$mageApp->getResponse()->setBody($success);
	}

	/**
	 * 2024-03-23 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Resolve the `Ebizmarts_MailChimp` module's issues found by IntelliJ IDEA inspections":
	 * https://github.com/thehcginstitute-com/m1/issues/530
	 * @override
	 * @see Mage_Adminhtml_Controller_Action::_isAllowed()
	 */
	protected function _isAllowed():bool {return in_array($this->getRequest()->getActionName(), [
		'createMergeFields'
		,'renderresendecom'
		,'resendEcommerceData'
		,'resetLocalErrors'
	]) && df_backend_session()->isAllowed('system/config/mailchimp');}

	/**
	 * @return Ebizmarts_MailChimp_Helper_Data
	 */
	protected function makeHelper() {return hcg_mc_h();}

	function addWarning($message)
	{
		Mage::getSingleton('core/session')->addWarning($message);
	}

	function addSuccess($message)
	{
		Mage::getSingleton('core/session')->addSuccess($message);
	}

	function addError($message)
	{
		Mage::getSingleton('core/session')->addError($message);
	}
}
