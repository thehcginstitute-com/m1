<?php
class Widgento_Login_IndexController extends Mage_Core_Controller_Front_Action {
	const REDIRECT_PATH = 'customer/account';
	const REQUEST_HASH  = 'id';

	/**
	 * @return Widgento_Login_Model_Login
	 */
	protected function getLoginModel()
	{
		return Mage::getModel('widgentologin/login');
	}

	/**
	 * @return Mage_Customer_Model_Session
	 */
	protected function getCustomerSession()
	{
		return Mage::getSingleton('customer/session');
	}

	/**
	 * @return Mage_Persistent_Model_Session
	 */
	protected function getPersistentSession()
	{
		return Mage::getSingleton('persistent/session');
	}

	/**
	 * @return Mage_Core_Model_Store
	 */
	protected function getCurrentStore()
	{
		return Mage::app()->getStore();
	}

	/**
	 * @used-by self::indexAction()
	 */
	private function getClearSingletonsList():array {
		return array(
			Mage::getSingleton('catalog/session'),
			Mage::getSingleton('catalogsearch/session'),
			Mage::getSingleton('core/session'),
			Mage::getSingleton('customer/session'),
			Mage::getSingleton('newsletter/session'),
			# 2024-03-18 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# «Call to a member function clear() on bool
			# in app/code/community/Widgento/Login/controllers/IndexController.php:73»:
			# https://github.com/thehcginstitute-com/m1/issues/520
			Mage::getSingleton('reports/session'),
			Mage::getSingleton('review/session'),
			Mage::getSingleton('wishlist/session'),
		);
	}

	function indexAction()
	{
		$hash     = $this->getRequest()->getParam(self::REQUEST_HASH);
		$login    = $this->getLoginModel()->load($hash, 'login_hash');
		$isActive = $login->getIsActive();
		$login->truncate();
		if ($isActive && $login->getCustomerId()) {
			Mage::dispatchEvent('widgentologin_before_login', array(
				'customer_id' => $login->getCustomerId(),
			));

			foreach ($this->getClearSingletonsList() as $singleton) {
				/* @var $singleton Mage_Core_Session_Abstract */
				# 2024-03-18 Dmitrii Fediuk https://upwork.com/fl/mage2pro
				# «Call to a member function clear() on bool
				# in app/code/community/Widgento/Login/controllers/IndexController.php:73»:
				# https://github.com/thehcginstitute-com/m1/issues/520
				if ($singleton) {
					$singleton->clear();
				}
			}

			if ($this->getCustomerSession()->getCustomerId()) {
				if ($this->getPersistentSession()) {
					$this->getPersistentSession()
						->clear()
						->deleteByCustomerId($this->getCustomerSession()->getCustomerId());
				}
			}

			if (method_exists($this->getCustomerSession(), 'renewSession')) {
				$this->getCustomerSession()->renewSession();
			}
			// for 1.4
			else {
				$this->getCustomerSession()->logout();
			}

			$this->getCustomerSession()->loginById($login->getCustomerId());

			Mage::dispatchEvent('widgentologin_after_login', array(
				'customer_id' => $login->getCustomerId(),
			));

			return $this->_redirect(static::REDIRECT_PATH);
		}

		return $this->_redirect('');
	}
}
