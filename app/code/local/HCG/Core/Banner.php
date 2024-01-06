<?php
use Mage_Core_Block_Template as B;
/**
 * 2019-04-11
 * «Make the header message editable in the Magento 1 backend via a CMS block»
 * https://www.upwork.com/ab/f/contracts/21957154
 */
final class HCG_Core_Banner {
	/**
	 * 2019-04-11
	 * @return string
	 */
	static function p() {
		static $r; /** @var string $r */
		if (!$r) {
			$b = \Mage::app()->getLayout()->createBlock(B::class); /** @var B $b */
			$b->setTemplate('hcg/core/banner.phtml');
			$r = $b->toHtml();
		}
		return $r;
	}
}