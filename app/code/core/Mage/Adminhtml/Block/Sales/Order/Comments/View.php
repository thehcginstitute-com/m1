<?php
class Mage_Adminhtml_Block_Sales_Order_Comments_View extends Mage_Adminhtml_Block_Template {
	/**
	 * Retrieve required options from parent
	 */
	protected function _beforeToHtml()
	{
		if (!$this->getParentBlock()) {
			Mage::throwException(Mage::helper('adminhtml')->__('Invalid parent block for this block.'));
		}
		$this->setEntity($this->getParentBlock()->getSource());
		return parent::_beforeToHtml();
	}

	/**
	 * @inheritDoc
	 */
	protected function _prepareLayout()
	{
		$button = $this->getLayout()->createBlock('adminhtml/widget_button')
			->setData([
				'id'      => 'submit_comment_button',
				'label'   => Mage::helper('sales')->__('Submit Comment'),
				'class'   => 'save'
			]);
		$this->setChild('submit_button', $button);

		return parent::_prepareLayout();
	}

	function getSubmitUrl()
	{
		return $this->getUrl('*/*/addComment', ['id' => $this->getEntity()->getId()]);
	}

	/**
     * 2024-09-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--5/app/design/adminhtml/default/default/layout/sales.xml#L251
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--5/app/design/adminhtml/default/default/layout/sales.xml#L377
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--5/app/design/adminhtml/default/default/layout/sales.xml#L535
	 */
	final function setParentType(string $v):void {$this->_parentType = $v;}

	/**
	 * 2024-09-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::canSendCommentEmail()
	 * @used-by self::setParentType()
	 * @var string
	 */
	private $_parentType = '';

	function canSendCommentEmail() {
		switch ($this->_parentType) {
			case 'invoice':
				return Mage::helper('sales')->canSendInvoiceCommentEmail(
					$this->getEntity()->getOrder()->getStore()->getId()
				);
			case 'shipment':
				return Mage::helper('sales')->canSendShipmentCommentEmail(
					$this->getEntity()->getOrder()->getStore()->getId()
				);
			case 'creditmemo':
				return Mage::helper('sales')->canSendCreditmemoCommentEmail(
					$this->getEntity()->getOrder()->getStore()->getId()
				);
		}
		return true;
	}

	/**
	 * Replace links in string
	 *
	 * @param array|string $data
	 * @param null|array $allowedTags
	 * @return string
	 */
	function escapeHtml($data, $allowedTags = null)
	{
		return Mage::helper('adminhtml/sales')->escapeHtmlWithLinks($data, $allowedTags);
	}
}
