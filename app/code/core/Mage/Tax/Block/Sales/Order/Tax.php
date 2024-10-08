<?php
# 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
use Mage_Sales_Block_Order_Creditmemo_Totals as TotalsC;
use Mage_Sales_Block_Order_Invoice_Totals as TotalsI;
use Mage_Sales_Block_Order_Totals as TotalsO;
class Mage_Tax_Block_Sales_Order_Tax extends Mage_Core_Block_Template
{
	/**
	 * Tax configuration model
	 *
	 * @var Mage_Tax_Model_Config
	 */
	protected $_config;

	/**
	 * @var Mage_Sales_Model_Order
	 */
	protected $_order;

	/**
	 * @var Mage_Sales_Model_Order_Invoice
	 */
	protected $_source;

	/**
	 * Initialize configuration object
	 */
	protected function _construct()
	{
		$this->_config = Mage::getSingleton('tax/config');
	}

	/**
	 * Check if we nedd display full tax total info
	 *
	 * @return bool
	 */
	function displayFullSummary()
	{
		return $this->_config->displaySalesFullSummary($this->getOrder()->getStore());
	}

	/**
	 * Get data (totals) source model
	 *
	 * @return Varien_Object
	 */
	function getSource()
	{
		return $this->_source;
	}

	/**
	 * Initialize all order totals relates with tax
	 *
	 * @return $this
	 */
	function initTotals()
	{
		/** @var Mage_Adminhtml_Block_Sales_Order_Invoice_Totals $parent */
		$parent = $this->getParentBlock();
		$this->_order   = $parent->getOrder();
		$this->_source  = $parent->getSource();

		$store = $this->getStore();
		$allowTax = ($this->_source->getTaxAmount() > 0) || ($this->_config->displaySalesZeroTax($store));
		$grandTotal = (float) $this->_source->getGrandTotal();
		if (!$grandTotal || ($allowTax && !$this->_config->displaySalesTaxWithGrandTotal($store))) {
			$this->_addTax();
		}

		$this->_initSubtotal();
		$this->_initShipping();
		$this->_initDiscount();
		$this->_initGrandTotal();
		return $this;
	}

	/**
	 * Add tax total string
	 *
	 * @param string $after
	 * @return $this
	 */
	protected function _addTax($after = 'discount')
	{
		$taxTotal = new Varien_Object([
			'code'      => 'tax',
			'block_name' => $this->getNameInLayout()
		]);
		$this->getParentBlock()->addTotal($taxTotal, $after);
		return $this;
	}

	/**
	 * Get order store object
	 *
	 * @return Mage_Core_Model_Store
	 */
	function getStore()
	{
		return $this->_order->getStore();
	}

	/**
	 * @return $this
	 */
	protected function _initSubtotal()
	{
		$store  = $this->getStore();
		$parent = $this->getParentBlock();
		$subtotal = $parent->getTotal('subtotal');
		if (!$subtotal) {
			return $this;
		}
		if ($this->_config->displaySalesSubtotalBoth($store)) {
			$subtotal       = (float) $this->_source->getSubtotal();
			$baseSubtotal   = (float) $this->_source->getBaseSubtotal();
			$subtotalIncl   = (float) $this->_source->getSubtotalInclTax();
			$baseSubtotalIncl = (float) $this->_source->getBaseSubtotalInclTax();

			if (!$subtotalIncl || !$baseSubtotalIncl) {
				//Calculate the subtotal if not set
				$subtotalIncl = $subtotal + $this->_source->getTaxAmount()
					- $this->_source->getShippingTaxAmount();
				$baseSubtotalIncl = $baseSubtotal + $this->_source->getBaseTaxAmount()
					- $this->_source->getBaseShippingTaxAmount();

				if ($this->_source instanceof Mage_Sales_Model_Order) {
					//Adjust the discount amounts for the base and well as the weee to display the right totals
					foreach ($this->_source->getAllItems() as $item) {
						# 2024-02-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
						# "Delete the unused `Mage_Weee` module": https://github.com/thehcginstitute-com/m1/issues/377
						$subtotalIncl += $item->getHiddenTaxAmount();
						$baseSubtotalIncl += $item->getBaseHiddenTaxAmount();
					}
				}
			}

			$subtotalIncl = max(0, $subtotalIncl);
			$baseSubtotalIncl = max(0, $baseSubtotalIncl);
			$totalExcl = new Varien_Object([
				'code'      => 'subtotal_excl',
				'value'     => $subtotal,
				'base_value' => $baseSubtotal,
				'label'     => $this->__('Subtotal (Excl.Tax)')
			]);
			$totalIncl = new Varien_Object([
				'code'      => 'subtotal_incl',
				'value'     => $subtotalIncl,
				'base_value' => $baseSubtotalIncl,
				'label'     => $this->__('Subtotal (Incl.Tax)')
			]);
			$parent->addTotal($totalExcl, 'subtotal');
			$parent->addTotal($totalIncl, 'subtotal_excl');
			$parent->removeTotal('subtotal');
		} elseif ($this->_config->displaySalesSubtotalInclTax($store)) {
			$subtotalIncl   = (float) $this->_source->getSubtotalInclTax();
			$baseSubtotalIncl = (float) $this->_source->getBaseSubtotalInclTax();

			if (!$subtotalIncl) {
				$subtotalIncl = $this->_source->getSubtotal()
					+ $this->_source->getTaxAmount()
					- $this->_source->getShippingTaxAmount();
			}
			if (!$baseSubtotalIncl) {
				$baseSubtotalIncl = $this->_source->getBaseSubtotal()
					+ $this->_source->getBaseTaxAmount()
					- $this->_source->getBaseShippingTaxAmount();
			}

			$total = $parent->getTotal('subtotal');
			if ($total) {
				$total->setValue(max(0, $subtotalIncl));
				$total->setBaseValue(max(0, $baseSubtotalIncl));
			}
		}
		return $this;
	}

	/**
	 * @return $this
	 */
	protected function _initShipping()
	{
		$store  = $this->getStore();
		$parent = $this->getParentBlock();
		$shipping = $parent->getTotal('shipping');
		if (!$shipping) {
			return $this;
		}

		if ($this->_config->displaySalesShippingBoth($store)) {
			$shipping           = (float) $this->_source->getShippingAmount();
			$baseShipping       = (float) $this->_source->getBaseShippingAmount();
			$shippingIncl       = (float) $this->_source->getShippingInclTax();
			if (!$shippingIncl) {
				$shippingIncl   = $shipping + (float) $this->_source->getShippingTaxAmount();
			}
			$baseShippingIncl   = (float) $this->_source->getBaseShippingInclTax();
			if (!$baseShippingIncl) {
				$baseShippingIncl = $baseShipping + (float) $this->_source->getBaseShippingTaxAmount();
			}

			$totalExcl = new Varien_Object([
				'code'      => 'shipping',
				'value'     => $shipping,
				'base_value' => $baseShipping,
				'label'     => $this->__('Shipping & Handling (Excl.Tax)')
			]);
			$totalIncl = new Varien_Object([
				'code'      => 'shipping_incl',
				'value'     => $shippingIncl,
				'base_value' => $baseShippingIncl,
				'label'     => $this->__('Shipping & Handling (Incl.Tax)')
			]);
			$parent->addTotal($totalExcl, 'shipping');
			$parent->addTotal($totalIncl, 'shipping');
		} elseif ($this->_config->displaySalesShippingInclTax($store)) {
			$shippingIncl       = $this->_source->getShippingInclTax();
			if (!$shippingIncl) {
				$shippingIncl = $this->_source->getShippingAmount()
					+ $this->_source->getShippingTaxAmount();
			}
			$baseShippingIncl   = $this->_source->getBaseShippingInclTax();
			if (!$baseShippingIncl) {
				$baseShippingIncl = $this->_source->getBaseShippingAmount()
					+ $this->_source->getBaseShippingTaxAmount();
			}
			$total = $parent->getTotal('shipping');
			if ($total) {
				$total->setValue($shippingIncl);
				$total->setBaseValue($baseShippingIncl);
			}
		}
		return $this;
	}

	protected function _initDiscount()
	{
	}

	/**
	 * @return $this
	 */
	protected function _initGrandTotal()
	{
		$store  = $this->getStore();
		$parent = $this->getParentBlock();
		$grandototal = $parent->getTotal('grand_total');
		if (!$grandototal || !(float)$this->_source->getGrandTotal()) {
			return $this;
		}

		if ($this->_config->displaySalesTaxWithGrandTotal($store)) {
			$grandtotal         = $this->_source->getGrandTotal();
			$baseGrandtotal     = $this->_source->getBaseGrandTotal();
			$grandtotalExcl     = $grandtotal - $this->_source->getTaxAmount();
			$baseGrandtotalExcl = $baseGrandtotal - $this->_source->getBaseTaxAmount();
			$grandtotalExcl     = max($grandtotalExcl, 0);
			$baseGrandtotalExcl = max($baseGrandtotalExcl, 0);
			$totalExcl = new Varien_Object([
				'code'      => 'grand_total',
				'strong'    => true,
				'value'     => $grandtotalExcl,
				'base_value' => $baseGrandtotalExcl,
				'label'     => $this->__('Grand Total (Excl.Tax)')
			]);
			$totalIncl = new Varien_Object([
				'code'      => 'grand_total_incl',
				'strong'    => true,
				'value'     => $grandtotal,
				'base_value' => $baseGrandtotal,
				'label'     => $this->__('Grand Total (Incl.Tax)')
			]);
			$parent->addTotal($totalExcl, 'grand_total');
			$this->_addTax('grand_total');
			$parent->addTotal($totalIncl, 'tax');
		}
		return $this;
	}

	/**
	 * @return Mage_Sales_Model_Order
	 */
	function getOrder()
	{
		return $this->_order;
	}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::_addTax()
	 * @used-by self::_initGrandTotal()
	 * @used-by self::_initShipping()
	 * @used-by self::_initSubtotal()
	 * @used-by self::getLabelProperties()
	 * @used-by self::getValueProperties()
	 * @used-by self::initTotals()
	 * @return TotalsC|TotalsI|TotalsO
	 */
	function getParentBlock():TotalsO {return parent::getParentBlock();}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by app/design/frontend/base/default/template/tax/order/tax.phtml
	 */
	final function getLabelProperties():?string {return $this->getParentBlock()->getLabelProperties();}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by app/design/frontend/base/default/template/tax/order/tax.phtml
	 */
	final function getValueProperties():?string {return $this->getParentBlock()->getValueProperties();}
}
