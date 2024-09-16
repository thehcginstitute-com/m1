<?php
abstract class Mage_Catalog_Block_Seo_Sitemap_Abstract extends Mage_Core_Block_Template {
	/**
	 * Init pager
	 * 2024-09-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @see \Mage_Catalog_Block_Seo_Sitemap_Tree_Category::bindPager()
	 */
	function bindPager(string $block):void {
		$pager = $this->getLayout()->getBlock($block);
		/** @var Mage_Page_Block_Html_Pager $pager */
		if ($pager) {
			$pager->setAvailableLimit([50 => 50]);
			$pager->setCollection($this->getCollection());
			$pager->setShowPerPage(false);
		}
	}

	/**
	 * Get item URL
	 *
	 * In most cases should be overridden in descendant blocks
	 *
	 * @param Mage_Catalog_Block_Seo_Sitemap_Abstract $item
	 * @return string
	 */
	function getItemUrl($item) {return $item->getUrl();}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by app/design/frontend/base/default/template/catalog/seo/sitemap.phtml
	 * @used-by app/design/frontend/base/default/template/catalog/seo/tree.phtml
	 */
	final protected function getItemsTitle():string {return $this->_itemsTitle;}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--12/app/design/frontend/default/mobileshoppe/layout/catalog.xml#L405
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--12/app/design/frontend/default/mobileshoppe/layout/catalog.xml#L463
	 */
	final function setItemsTitle(string $v):void {$this->_itemsTitle = $v;}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::getItemsTitle()
	 * @used-by self::setItemsTitle()
	 * @var string
	 */
	private $_itemsTitle;
}
