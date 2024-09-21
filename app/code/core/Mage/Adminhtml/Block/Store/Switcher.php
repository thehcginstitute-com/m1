<?php
# 2024-09-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
class Mage_Adminhtml_Block_Store_Switcher extends Mage_Adminhtml_Block_Template {
	/**
	 * @var array
	 */
	protected $_storeIds;

	/**
	 * Name of store variable
	 *
	 * @var string
	 */
	protected $_storeVarName = 'store';

	/**
	 * @var bool
	 */
	protected $_hasDefaultOption = true;

	function __construct()
	{
		parent::__construct();
		$this->setTemplate('store/switcher.phtml');
		$this->setUseConfirm(true);
		$this->setUseAjax(true);
		$this->setDefaultStoreName($this->__('All Store Views'));
	}

	/**
	 * 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 */
	final function getUseConfirm():bool {return $this[self::$USE_CONFIRM];}

	/**
     * 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 */
	final function setUseConfirm(bool $v):void {$this[self::$USE_CONFIRM] = $v;}

	/**
	 * 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::getUseConfirm()
	 * @used-by self::setUseConfirm()
	 * @const string
	 */
	private static $USE_CONFIRM = 'use_confirm';

	/**
	 * @return Mage_Core_Model_Resource_Website_Collection
	 * @throws Mage_Core_Exception
	 * @deprecated
	 */
	function getWebsiteCollection()
	{
		$collection = Mage::getModel('core/website')->getResourceCollection();

		$websiteIds = $this->getWebsiteIds();
		if (!is_null($websiteIds)) {
			$collection->addIdFilter($this->getWebsiteIds());
		}

		return $collection->load();
	}

	/**
	 * Get websites
	 *
	 * @return array
	 */
	function getWebsites()
	{
		$websites = Mage::app()->getWebsites();
		if ($websiteIds = $this->getWebsiteIds()) {
			foreach ($websites as $websiteId => $website) {
				if (!in_array($websiteId, $websiteIds)) {
					unset($websites[$websiteId]);
				}
			}
		}
		return $websites;
	}

	/**
	 * @param Mage_Core_Model_Website|int|string $website
	 * @return Mage_Core_Model_Resource_Store_Group_Collection
	 * @deprecated
	 */
	function getGroupCollection($website)
	{
		if (!$website instanceof Mage_Core_Model_Website) {
			$website = Mage::getModel('core/website')->load($website);
		}
		return $website->getGroupCollection();
	}

	/**
	 * Get store groups for specified website
	 *
	 * @param Mage_Core_Model_Website|int|string|null $website
	 * @return array
	 */
	function getStoreGroups($website)
	{
		if (!$website instanceof Mage_Core_Model_Website) {
			$website = Mage::app()->getWebsite($website);
		}
		return $website->getGroups();
	}

	/**
	 * @param Mage_Core_Model_Store_Group|int|string $group
	 * @return Mage_Core_Model_Resource_Store_Collection
	 * @deprecated
	 */
	function getStoreCollection($group)
	{
		if (!$group instanceof Mage_Core_Model_Store_Group) {
			$group = Mage::getModel('core/store_group')->load($group);
		}
		$stores = $group->getStoreCollection();
		$_storeIds = $this->getStoreIds();
		if (!empty($_storeIds)) {
			$stores->addIdFilter($_storeIds);
		}
		return $stores;
	}

	/**
	 * Get store views for specified store group
	 *
	 * @param Mage_Core_Model_Store_Group|int|string|null $group
	 * @return array
	 */
	function getStores($group)
	{
		if (!$group instanceof Mage_Core_Model_Store_Group) {
			$group = Mage::app()->getGroup($group);
		}
		$stores = $group->getStores();
		if ($storeIds = $this->getStoreIds()) {
			foreach ($stores as $storeId => $store) {
				if (!in_array($storeId, $storeIds)) {
					unset($stores[$storeId]);
				}
			}
		}
		return $stores;
	}

	/**
	 * @return string
	 */
	function getSwitchUrl()
	{
		if ($url = $this->getData('switch_url')) {
			return $url;
		}
		return $this->getUrl('*/*/*', ['_current' => true, $this->_storeVarName => null]);
	}

	/**
	 * 2024-09-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--9/app/design/adminhtml/default/default/layout/iwd_admin_checkout.xml#L19
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--9/app/design/adminhtml/default/default/layout/iwd_admin_checkout.xml#L46
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--9/app/design/adminhtml/default/default/layout/report.xml#L31
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--9/app/design/adminhtml/default/default/layout/sales.xml#L1156
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--9/app/design/adminhtml/default/default/layout/sales.xml#L1175
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--9/app/design/adminhtml/default/default/layout/sales.xml#L1198
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--9/app/design/adminhtml/default/default/layout/sales.xml#L1216
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--9/app/design/adminhtml/default/default/layout/sales.xml#L1234
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--9/app/design/adminhtml/default/default/layout/sales.xml#L1252
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--9/app/design/adminhtml/default/default/layout/sales.xml#L1275
	 */
	final function k_store_ids():void {$this->_storeVarName = 'store_ids';}

	/**
	 * @return mixed
	 * @throws Exception
	 */
	function getStoreId()
	{
		return $this->getRequest()->getParam($this->_storeVarName);
	}

	/**
	 * @param array $storeIds
	 * @return $this
	 */
	function setStoreIds($storeIds)
	{
		$this->_storeIds = $storeIds;
		return $this;
	}

	/**
	 * @return array
	 */
	function getStoreIds()
	{
		return $this->_storeIds;
	}

	/**
	 * @return bool
	 */
	function isShow()
	{
		return !Mage::app()->isSingleStoreMode();
	}

	/**
	 * @return string
	 */
	protected function _toHtml()
	{
		if (!Mage::app()->isSingleStoreMode()) {
			return parent::_toHtml();
		}
		return '';
	}

	/**
	 * Set/Get whether the switcher should show default option
	 *
	 * @param bool $hasDefaultOption
	 * @return bool
	 */
	function hasDefaultOption($hasDefaultOption = null)
	{
		if ($hasDefaultOption !== null) {
			$this->_hasDefaultOption = $hasDefaultOption;
		}
		return $this->_hasDefaultOption;
	}
}
