<?php
/**
 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * @method $this setBodyClass(string $value)
 */
class Mage_Page_Block_Html extends Mage_Core_Block_Template {
	function __construct() {
		parent::__construct();
		$this->_urls = [
			'base'      => Mage::getBaseUrl('web'),
			'baseSecure' => Mage::getBaseUrl('web', true),
			'current'   => $this->getRequest()->getRequestUri()
		];
		$action = Mage::app()->getFrontController()->getAction();
		if ($action) {
			$this->addBodyClass($action->getFullActionName('-'));
		}
		$this->_beforeCacheUrl();
	}

	/**
	 * Add CSS class to page body tag
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 */
	function addBodyClass(string $className):self {
		$className = preg_replace('#[^a-z0-9]+#', '-', strtolower($className));
		$class = $this->getBodyClass() ? $this->getBodyClass() . ' ' . $className : $className;
		$this->setBodyClass($class);
		return $this;
	}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by Mage_Page_Helper_Layout::applyTemplate()
	 */
	final function canUseACustomTemplate():bool {return $this->_canUseACustomTemplate;}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 */
	function getAbsoluteFooter():string	{return Mage::getStoreConfig('design/footer/absolute_footer');}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 */
	final function getBaseSecureUrl():string {return $this->_urls['baseSecure'];}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 */
	final function getBaseUrl():string {return $this->_urls['base'];}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * 2024-09-21
	 * «Mage_Page_Block_Html::getBodyClass(): Return value must be of type string, null returned»:
	 * https://github.com/thehcginstitute-com/m1/issues/682
	 */
	final function getBodyClass():string {return df_ets($this->_getData('body_class'));}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 */
	final function getCurrentUrl():string	{return $this->_urls['current'];}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 */
	final function getHeaderTitle():string {return $this->_title;}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 */
	function getLang():string {
		if (!$this->hasData('lang')) {
			$this->setData('lang', substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2));
		}
		return $this->getData('lang');
	}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 */
	function getPrintLogoText():string {return Mage::getStoreConfig('sales/identity/address');}

	/**
	 * Print Logo URL (Conf -> Sales -> Invoice and Packing Slip Design)
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 */
	function getPrintLogoUrl():string {
		// load html logo
		$logo = Mage::getStoreConfig('sales/identity/logo_html');
		if (!empty($logo)) {
			$logo = 'sales/store/logo_html/' . $logo;
		}
		// load default logo
		if (empty($logo)) {
			$logo = Mage::getStoreConfig('sales/identity/logo');
			if (!empty($logo)) {
				// prevent tiff format displaying in html
				if (strtolower(substr($logo, -5)) === '.tiff' || strtolower(substr($logo, -4)) === '.tif') {
					$logo = '';
				} else {
					$logo = 'sales/store/logo/' . $logo;
				}
			}
		}
		// buld url
		if (!empty($logo)) {
			$logo = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_MEDIA_URL) . $logo;
		} else {
			$logo = '';
		}
		return $logo;
	}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--10/app/design/frontend/default/mobileshoppe/layout/page.xml#L129
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--10/app/design/frontend/default/mobileshoppe/layout/page.xml#L137
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--10/app/design/frontend/default/mobileshoppe/layout/page.xml#L145
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--10/app/design/frontend/default/mobileshoppe/layout/page.xml#L153
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--10/app/design/frontend/default/mobileshoppe/layout/page.xml#L161
	 */
	final function preventCustomTemplates():void {$this->_canUseACustomTemplate = false;}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @used-by Mage_Customer_Block_Account::__construct()
	 * @used-by Mage_Sales_Block_Order_Details::__construct()
	 * @used-by Mage_Sales_Block_Order_History::__construct()
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--6/app/design/frontend/base/default/layout/downloadable.xml#L27
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--6/app/design/frontend/default/mobileshoppe/layout/contacts.xml#L24
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--6/app/design/frontend/default/mobileshoppe/layout/customer.xml#L99
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--6/app/design/frontend/default/mobileshoppe/layout/customer.xml#L114
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--6/app/design/frontend/default/mobileshoppe/layout/customer.xml#L130
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--6/app/design/frontend/default/mobileshoppe/layout/customer.xml#L140
	 */
	final function setHeaderTitle(string $v):void {$this->_title = $v;}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by Mage_Page_Helper_Layout::getCurrentPageLayout()
	 */
	final function getLayoutCode():?string {return $this[self::$LAYOUT_CODE];}

	/**
     * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--15/app/design/frontend/default/mobileshoppe/layout/page.xml#L130
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--15/app/design/frontend/default/mobileshoppe/layout/page.xml#L138
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--15/app/design/frontend/default/mobileshoppe/layout/page.xml#L146
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--15/app/design/frontend/default/mobileshoppe/layout/page.xml#L154
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--15/app/design/frontend/default/mobileshoppe/layout/page.xml#L163
	 */
	final function setLayoutCode(string $v):void {$this[self::$LAYOUT_CODE] = $v;}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::getLayoutCode()
	 * @used-by self::setLayoutCode()
	 * @const string
	 */
	private static $LAYOUT_CODE = 'layout_code';

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @throws Mage_Core_Exception
	 */
	function setTheme(string $theme):self {
		$arr = explode('/', $theme);
		if (isset($arr[1])) {
			Mage::getDesign()->setPackageName($arr[0])->setTheme($arr[1]);
		} else {
			Mage::getDesign()->setTheme($theme);
		}
		return $this;
	}

	/**
	 * Processing block html after rendering
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @override
	 * @see Mage_Core_Block_Abstract::_afterToHtml()
	 * @param string $html
	 */
	protected function _afterToHtml($html):string {return $this->_afterCacheUrl($html);}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @var string[]
	 */
	protected $_urls = [];

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @var string
	 */
	protected $_title = '';

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::canUseACustomTemplate()
	 * @used-by self::preventCustomTemplates()
	 * @var bool
	 */
	private $_canUseACustomTemplate = true;
}