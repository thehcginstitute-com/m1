<?php
/**
 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * @see Mage_Adminhtml_Block_Sales_Totals
 * @see Mage_Sales_Block_Order_Creditmemo_Totals
 * @see Mage_Sales_Block_Order_Invoice_Totals
 */
class Mage_Sales_Block_Order_Totals extends Mage_Core_Block_Template {
	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by Mage_Tax_Block_Sales_Order_Tax::getLabelProperties()
	 * @used-by app/design/adminhtml/default/default/template/sales/order/totals.phtml
	 * @used-by app/design/frontend/base/default/template/sales/order/totals.phtml
	 */
	final function getLabelProperties():?string {return $this[self::$LABEL_PROPERTIES];}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by Mage_Tax_Block_Sales_Order_Tax::getValueProperties()
	 * @used-by app/design/adminhtml/default/default/template/sales/order/totals.phtml
	 * @used-by app/design/frontend/base/default/template/sales/order/totals.phtml
	 */
	final function getValueProperties():?string {return $this[self::$VALUE_PROPERTIES];}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--13/app/design/frontend/default/mobileshoppe/layout/sales.xml#L73
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--13/app/design/frontend/default/mobileshoppe/layout/sales.xml#L124
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--13/app/design/frontend/default/mobileshoppe/layout/sales.xml#L216
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--13/app/design/frontend/default/mobileshoppe/layout/sales.xml#L269
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--13/app/design/frontend/default/mobileshoppe/layout/sales.xml#L297
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--13/app/design/frontend/default/mobileshoppe/layout/sales.xml#L334
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--13/app/design/frontend/default/mobileshoppe/layout/sales.xml#L356
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--13/app/design/frontend/default/mobileshoppe/layout/sales.xml#L381
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--13/app/design/frontend/default/mobileshoppe/layout/sales.xml#L411
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--13/app/design/frontend/default/mobileshoppe/layout/sales.xml#L454
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--13/app/design/frontend/default/mobileshoppe/layout/sales.xml#L507
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--13/app/design/frontend/default/mobileshoppe/layout/sales.xml#L605
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--13/app/design/frontend/default/mobileshoppe/layout/sales.xml#L660
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--13/app/design/frontend/default/mobileshoppe/layout/sales.xml#L688
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--13/app/design/frontend/default/mobileshoppe/layout/sales.xml#L725
	 */
	final function setLabelProperties(string $v):void {$this[self::$LABEL_PROPERTIES] = $v;}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--14/app/design/frontend/default/mobileshoppe/layout/sales.xml#L74
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--14/app/design/frontend/default/mobileshoppe/layout/sales.xml#L123
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--14/app/design/frontend/default/mobileshoppe/layout/sales.xml#L213
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--14/app/design/frontend/default/mobileshoppe/layout/sales.xml#L264
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--14/app/design/frontend/default/mobileshoppe/layout/sales.xml#L290
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--14/app/design/frontend/default/mobileshoppe/layout/sales.xml#L325
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--14/app/design/frontend/default/mobileshoppe/layout/sales.xml#L345
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--14/app/design/frontend/default/mobileshoppe/layout/sales.xml#L368
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--14/app/design/frontend/default/mobileshoppe/layout/sales.xml#L396
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--14/app/design/frontend/default/mobileshoppe/layout/sales.xml#L437
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--14/app/design/frontend/default/mobileshoppe/layout/sales.xml#L488
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--14/app/design/frontend/default/mobileshoppe/layout/sales.xml#L584
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--14/app/design/frontend/default/mobileshoppe/layout/sales.xml#L637
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--14/app/design/frontend/default/mobileshoppe/layout/sales.xml#L663
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--14/app/design/frontend/default/mobileshoppe/layout/sales.xml#L698
	 */
	final function setValueProperties(string $v):void {$this[self::$VALUE_PROPERTIES] = $v;}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::getLabelProperties()
	 * @used-by self::setLabelProperties()
	 * @const string
	 */
	private static $LABEL_PROPERTIES = 'label_properties';

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::getValueProperties()
	 * @used-by self::setValueProperties()
	 * @const string
	 */
	private static $VALUE_PROPERTIES = 'value_properties';

	/**
	 * Associated array of totals
	 * array(
	 *  $totalCode => $totalObject
	 * )
	 *
	 * @var array
	 */
	protected $_totals;
	protected $_order = null;

	/**
	 * Initialize self totals and children blocks totals before html building
	 *
	 * @inheritDoc
	 */
	protected function _beforeToHtml()
	{
		$this->_initTotals();
		foreach ($this->getChild() as $child) {
			if (method_exists($child, 'initTotals')) {
				$child->initTotals();
			}
		}
		return parent::_beforeToHtml();
	}

	/**
	 * Get order object
	 *
	 * @return Mage_Sales_Model_Order
	 */
	function getOrder()
	{
		if ($this->_order === null) {
			if ($this->hasData('order')) {
				$this->_order = $this->_getData('order');
			} elseif (Mage::registry('current_order')) {
				$this->_order = Mage::registry('current_order');
			} elseif ($this->getParentBlock()->getOrder()) {
				$this->_order = $this->getParentBlock()->getOrder();
			}
		}
		return $this->_order;
	}

	/**
	 * @param Mage_Sales_Model_Order $order
	 * @return $this
	 */
	function setOrder($order)
	{
		$this->_order = $order;
		return $this;
	}

	/**
	 * Get totals source object
	 *
	 * @return Mage_Sales_Model_Order
	 */
	function getSource()
	{
		return $this->getOrder();
	}

	/**
	 * Initialize order totals array
	 *
	 * @return $this
	 */
	protected function _initTotals()
	{
		$source = $this->getSource();

		$this->_totals = [];
		$this->_totals['subtotal'] = new Varien_Object([
			'code'  => 'subtotal',
			'value' => $source->getSubtotal(),
			'label' => $this->__('Subtotal')
		]);

		/**
		 * Add shipping
		 */
		if (!$source->getIsVirtual() && ((float) $source->getShippingAmount() || $source->getShippingDescription())) {
			$this->_totals['shipping'] = new Varien_Object([
				'code'  => 'shipping',
				'field' => 'shipping_amount',
				'value' => $this->getSource()->getShippingAmount(),
				'label' => $this->__('Shipping & Handling')
			]);
		}

		/**
		 * Add discount
		 */
		if ((float)$this->getSource()->getDiscountAmount() != 0) {
			if ($this->getSource()->getDiscountDescription()) {
				$discountLabel = $this->__('Discount (%s)', $source->getDiscountDescription());
			} else {
				$discountLabel = $this->__('Discount');
			}
			$this->_totals['discount'] = new Varien_Object([
				'code'  => 'discount',
				'field' => 'discount_amount',
				'value' => $source->getDiscountAmount(),
				'label' => $discountLabel
			]);
		}

		$this->_totals['grand_total'] = new Varien_Object([
			'code'  => 'grand_total',
			'field'  => 'grand_total',
			'strong' => true,
			'value' => $source->getGrandTotal(),
			'label' => $this->__('Grand Total')
		]);

		/**
		 * Base grandtotal
		 */
		if ($this->getOrder()->isCurrencyDifferent()) {
			$this->_totals['base_grandtotal'] = new Varien_Object([
				'code'  => 'base_grandtotal',
				'value' => $this->getOrder()->formatBasePrice($source->getBaseGrandTotal()),
				'label' => $this->__('Grand Total to be Charged'),
				'is_formated' => true,
			]);
		}
		return $this;
	}

	/**
	 * Add new total to totals array after specific total or before last total by default
	 *
	 * @param   Varien_Object $total
	 * @param   null|string $after accepted values: 'first', 'last'
	 * @return  Mage_Sales_Block_Order_Totals
	 */
	function addTotal(Varien_Object $total, $after = null)
	{
		if ($after !== null && $after != 'last' && $after != 'first') {
			$totals = [];
			$added = false;
			foreach ($this->_totals as $code => $item) {
				$totals[$code] = $item;
				if ($code == $after) {
					$added = true;
					$totals[$total->getCode()] = $total;
				}
			}
			if (!$added) {
				$last = array_pop($totals);
				$totals[$total->getCode()] = $total;
				$totals[$last->getCode()] = $last;
			}
			$this->_totals = $totals;
		} elseif ($after == 'last') {
			$this->_totals[$total->getCode()] = $total;
		} elseif ($after == 'first') {
			$totals = [$total->getCode() => $total];
			$this->_totals = array_merge($totals, $this->_totals);
		} else {
			$last = array_pop($this->_totals);
			$this->_totals[$total->getCode()] = $total;
			$this->_totals[$last->getCode()] = $last;
		}
		return $this;
	}

	/**
	 * Add new total to totals array before specific total or after first total by default
	 *
	 * @param Varien_Object $total
	 * @param null|array|string $before
	 * @return  Mage_Sales_Block_Order_Totals
	 */
	function addTotalBefore(Varien_Object $total, $before = null)
	{
		if ($before !== null) {
			if (!is_array($before)) {
				$before = [$before];
			}
			foreach ($before as $beforeTotals) {
				if (isset($this->_totals[$beforeTotals])) {
					$totals = [];
					foreach ($this->_totals as $code => $item) {
						if ($code == $beforeTotals) {
							$totals[$total->getCode()] = $total;
						}
						$totals[$code] = $item;
					}
					$this->_totals = $totals;
					return $this;
				}
			}
		}
		$totals = [];
		$first = array_shift($this->_totals);
		$totals[$first->getCode()] = $first;
		$totals[$total->getCode()] = $total;
		foreach ($this->_totals as $code => $item) {
			$totals[$code] = $item;
		}
		$this->_totals = $totals;
		return $this;
	}

	/**
	 * Get Total object by code
	 *
	 * @param string $code
	 * @return Varien_Object|false
	 */
	function getTotal($code)
	{
		return $this->_totals[$code] ?? false;
	}

	/**
	 * Delete total by specific
	 *
	 * @param   string $code
	 * @return  Mage_Sales_Block_Order_Totals
	 */
	function removeTotal($code)
	{
		unset($this->_totals[$code]);
		return $this;
	}

	/**
	 * Apply sort orders to totals array.
	 * Array should have next structure
	 * array(
	 *  $totalCode => $totalSortOrder
	 * )
	 *
	 *
	 * @param   array $order
	 * @return  Mage_Sales_Block_Order_Totals
	 */
	function applySortOrder($order)
	{
		return $this;
	}

	/**
	 * get totals array for visualization
	 *
	 * @param null|string $area
	 * @return array
	 */
	function getTotals($area = null)
	{
		$totals = [];
		if ($area === null) {
			$totals = $this->_totals;
		} else {
			$area = (string)$area;
			foreach ($this->_totals as $total) {
				$totalArea = (string) $total->getArea();
				if ($totalArea == $area) {
					$totals[] = $total;
				}
			}
		}
		return $totals;
	}

	/**
	 * Format total value based on order currency
	 *
	 * @param   Varien_Object $total
	 * @return  string
	 */
	function formatValue($total)
	{
		if (!$total->getIsFormated()) {
			return $this->getOrder()->formatPrice($total->getValue());
		}
		return $total->getValue();
	}
}
