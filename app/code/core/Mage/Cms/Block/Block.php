<?php
class Mage_Cms_Block_Block extends Mage_Core_Block_Abstract {
	/**
     * 2024-09-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 */
	final function getBlockId():?string {return $this['block_id'];}

	/**
     * 2024-09-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @used-by IWD_OrderManager_Frontend_ConfirmController::_confirm()
	 * @used-by Mage_Catalog_Block_Category_View::getCmsBlockHtml()
	 * @used-by Mage_Core_Model_Email_Template_Filter::blockDirective()
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-14/app/design/frontend/default/mobileshoppe/layout/local.xml#L30()
	 */
	final function setBlockId(string $v):self {
		$this['block_id'] = $v;
		return $this;
	}

	/**
	 * Initialize cache
	 */
	protected function _construct()
	{
		/*
		* setting cache to save the cms block
		*/
		$this->setCacheTags([Mage_Cms_Model_Block::CACHE_TAG]);
		$this->setCacheLifetime(false);
	}

	/**
	 * Prepare Content HTML
	 *
	 * @return string
	 * @throws Mage_Core_Model_Store_Exception
	 * @throws Exception
	 */
	protected function _toHtml()
	{
		$blockId = $this->getBlockId();
		$html = '';
		if ($blockId) {
			$block = Mage::getModel('cms/block')
				->setStoreId(Mage::app()->getStore()->getId())
				->load($blockId);
			if ($block->getIsActive()) {
				/** @var Mage_Cms_Helper_Data $helper */
				$helper = Mage::helper('cms');
				$processor = $helper->getBlockTemplateProcessor();
				$html = $processor->filter($block->getContent());
				$this->addModelTags($block);
			}
		}
		return $html;
	}

	/**
	 * Retrieve values of properties that unambiguously identify unique content
	 *
	 * @return array
	 * @throws Mage_Core_Model_Store_Exception
	 */
	function getCacheKeyInfo()
	{
		$blockId = $this->getBlockId();
		if ($blockId) {
			$result = [
				'CMS_BLOCK',
				$blockId,
				Mage::app()->getStore()->getCode(),
			];
		} else {
			$result = parent::getCacheKeyInfo();
		}
		return $result;
	}
}
