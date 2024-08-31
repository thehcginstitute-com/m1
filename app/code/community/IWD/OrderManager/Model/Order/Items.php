<?php

class IWD_OrderManager_Model_Order_Items extends Mage_Sales_Model_Order_Item
{
    protected $params;
    protected $needUpdateStock = false;

    function updateOrderItems($params) {
        $this->init($params);
        if (isset($params['confirm_edit']) && !empty($params['confirm_edit'])) {
            $this->addChangesToConfirm();
        }
		else {
            $status = $this->editItems();
            $this->addChangesToLog();
            if ($status == 1) {
                $this->notifyEmail();
            }
        }
    }

    protected function init($params)
    {
        $this->params = $params;
        if (empty($this->params) || !isset($this->params['order_id'])) {
            throw new Exception("Order id is not defined");
        }
    }

    function getLogger()
    {
        return Mage::getSingleton('iwd_ordermanager/logger');
    }

    function getNeedUpdateStock()
    {
        return $this->needUpdateStock;
    }

    protected function addChangesToConfirm()
    {
        /* @var $logger IWD_OrderManager_Model_Logger */
        $logger = $this->getLogger();
        $orderId = $this->params['order_id'];
        $items = $this->params['items'];

        Mage::getModel('iwd_ordermanager/order_estimate')->estimateEditItems($orderId, $items);

        $logger->addCommentToOrderHistory($orderId, 'wait');
        $logger->addLogToLogTable(IWD_OrderManager_Model_Confirm_Options_Type::ITEMS, $orderId, $this->params);

        $message = Mage::helper('iwd_ordermanager')->__('Updates were not applied now! Customer gets email with confirm link. Order will be updated after confirm.');
        Mage::getSingleton('adminhtml/session')->addNotice($message);
    }

	/**
	 * 2024-08-31 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `IWD_OrderManager` module": https://github.com/cabinetsbay/site/issues/533
	 * @used-by self::updateOrderItems()
	 */
    private function editItems() {
        $orderId = isset($this->params['order_id']) ? $this->params['order_id'] : null;
		/**
		 * 2024-08-31 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		 * 1) "Refactor the `IWD_OrderManager` module": https://github.com/cabinetsbay/site/issues/533
		 * 2) `$items` is an array like:
		 *	{
		 *		"23371": {
		 *			"description": "",
		 *			"discount_amount": "0.00",
		 *			"discount_percent": "0.00",
		 *			"fact_qty": "1",
		 *			"hidden_tax_amount": "0.00",
		 *			"original_price": "379.00",
		 *			"price": "379.00",
		 *			"price_incl_tax": "379.00",
		 *			"product_id": "75",
		 *			"product_options": "",
		 *			"row_total": "379.00",
		 *			"sku": "ULT10-Immune-Vit-C-10mL-ULT10-Immune-Vit-C-SQ-3x-10mL",
		 *			"subtotal": "379.00",
		 *			"subtotal_incl_tax": "379.00",
		 *			"tax_amount": "0.00",
		 *			"tax_percent": "0.00"
		 *		},
		 *		"23378": {
		 *			"hidden_tax_amount": "0.00",
		 *			"original_price": "249.00",
		 *			"product_id": "161",
		 *			"product_options": "",
		 *			"remove": "1",
		 *			"sku": "b-complex-injections-x-30mL-sq-syringes"
		 *		}
		 *	}
		 */
		$items = isset($this->params['items']) ? $this->params['items'] : null;

        /**
         * @var $orderEdit IWD_OrderManager_Model_Order_Edit
         */
        $orderEdit = Mage::getModel('iwd_ordermanager/order_edit');
        $status = $orderEdit->editItems($orderId, $items);
        $this->needUpdateStock = $orderEdit->getNeedUpdateStock();

        $this->updateCoupon();

        return $status;
    }


    protected function notifyEmail()
    {
        $notify = isset($this->params['notify']) ? $this->params['notify'] : null;
        $orderId = $this->params['order_id'];

        if ($notify) {
            $message = isset($this->params['comment_text']) ? $this->params['comment_text'] : null;
            $email = isset($this->params['comment_email']) ? $this->params['comment_email'] : null;
            Mage::getModel('iwd_ordermanager/notify_notification')->sendNotifyEmail($orderId, $email, $message);
        }
    }

    protected function addChangesToLog()
    {
        /* @var $logger IWD_OrderManager_Model_Logger */
        $logger = $this->getLogger();
        $orderId = $this->params['order_id'];

        $logger->addCommentToOrderHistory($orderId, false);
        $logger->addLogToLogTable(IWD_OrderManager_Model_Confirm_Options_Type::ITEMS, $orderId);
    }

    protected function updateCoupon()
    {
        if (isset($this->params['coupon_code'])) {
            $couponCode = $this->params['coupon_code'];
            $this->loadOrder()->setCouponCode($couponCode)->setDiscountDescription($couponCode)->save();
        }
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    protected function loadOrder()
    {
        $orderId = $this->params['order_id'];
        return Mage::getModel('sales/order')->load($orderId);
    }
}
