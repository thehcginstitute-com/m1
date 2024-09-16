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

	function canSendCommentEmail()
	{
		switch ($this->getParentType()) {
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
