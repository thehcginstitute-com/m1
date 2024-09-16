<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payment information container block
 *
 * @category   Mage
 * @package    Mage_Payment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Payment_Block_Info_Container extends Mage_Core_Block_Template
{
	/**
	 * Add payment info block to layout
	 *
	 * @inheritDoc
	 */
	protected function _prepareLayout()
	{
		if ($info = $this->getPaymentInfo()) {
			$this->setChild(
				$this->_getInfoBlockName(),
				Mage::helper('payment')->getInfoBlock($info)
			);
		}
		return parent::_prepareLayout();
	}

	/**
	 * Retrieve info block name
	 *
	 * @return false|string
	 */
	protected function _getInfoBlockName()
	{
		if ($info = $this->getPaymentInfo()) {
			return 'payment.info.' . $info->getMethodInstance()->getCode();
		}
		return false;
	}

	/**
	 * Retrieve payment info model
	 *
	 * @return Mage_Payment_Model_Info|false
	 */
	function getPaymentInfo()
	{
		return false;
	}

	/**
     * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 */
	function setInfoTemplate(string $method = '', string $template = ''):self
	{
		if ($info = $this->getPaymentInfo()) {
			if ($info->getMethodInstance()->getCode() == $method) {
				$this->getChild($this->_getInfoBlockName())->setTemplate($template);
			}
		}
		return $this;
	}
}
