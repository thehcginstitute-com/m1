<?php
use HCG_Core_StaticContent as SC;
/**
 * 2024-01-06
 * "Port the modifications of `app/code/core/Mage/Page/Block/Html/Head.php` to Magento 1.9.4.5":
 * https://github.com/thehcginstitute-com/m1/issues/97
 * 2024-04-24
 * "Add the `?v=<version>` suffix to the JavaScript files loaded in the Magento's backend
 * from the `/js/` folder (similar to theme JavaScript and CSS files)":
 * https://github.com/thehcginstitute-com/m1/issues/583
 * @see Mage_Adminhtml_Block_Page_Head
 */
class HCG_Page_Block_Html_Head extends Mage_Page_Block_Html_Head {
	/**
	 * 2024-01-06 https://github.com/magento-russia/3/blob/2023-07-10/app/code/local/Df/Page/Block/Html/Head.php#L84-L104
	 * @override
	 * @see Mage_Page_Block_Html_Head::_prepareStaticAndSkinElements()
	 * @used-by Mage_Page_Block_Html_Head::getCssJsHtml()
	 * @param string $format - HTML element format for sprintf('<element src="%s"%s />', $src, $params)
	 * @param array $staticItems - array of relative names of static items to be grabbed from js/ folder
	 * @param array $skinItems - array of relative names of skin items to be found in skins according to design config
	 * @param callback $mergeCallback
	 */
	protected function &_prepareStaticAndSkinElements(
		$format, array $staticItems, array $skinItems, $mergeCallback = null
	):string {return parent::_prepareStaticAndSkinElements(
		$format, $mergeCallback ? $staticItems : self::addVersionStamp($staticItems), $skinItems, $mergeCallback
	);}

	/**
	 * 2018-09-24
	 * @used-by self::_prepareStaticAndSkinElements()
	 * @param array $staticItems
	 * @return array
	 */
	private static function addVersionStamp(array $staticItems) {
		foreach ($staticItems as &$rows) {
			foreach ($rows as &$name) {
				$name .= '?v=' . SC::V;
			}
		}
		return $staticItems;
	}
}