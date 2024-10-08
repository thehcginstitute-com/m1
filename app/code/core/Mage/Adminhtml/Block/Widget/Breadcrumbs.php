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
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml page breadcrumbs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Widget_Breadcrumbs extends Mage_Adminhtml_Block_Template
{
	/**
	 * breadcrumbs links
	 *
	 * @var array
	 */
	protected $_links = [];

	/**
	 * Mage_Adminhtml_Block_Widget_Breadcrumbs constructor.
	 */
	function __construct()
	{
		$this->setTemplate('widget/breadcrumbs.phtml');
		$this->addLink(Mage::helper('adminhtml')->__('Home'), Mage::helper('adminhtml')->__('Home'), $this->getUrl('*'));
	}

	/**
     * 2024-09-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @used-by self::__construct()
	 * @used-by Mage_Adminhtml_Block_Widget::_addBreadcrumb()
	 * @used-by Mage_Adminhtml_Controller_Action::_addBreadcrumb()
	 */
	final function addLink(string $label, ?string $title = '', ?string $url = ''):self	{
		if (empty($title)) {
			$title = $label;
		}
		$this->_links[] = [
			'label' => $label,
			'title' => $title,
			'url'   => $url
		];
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	protected function _beforeToHtml()
	{
		// TODO - Moved to Beta 2, no breadcrumbs displaying in Beta 1
		// $this->assign('links', $this->_links);
		return parent::_beforeToHtml();
	}
}
