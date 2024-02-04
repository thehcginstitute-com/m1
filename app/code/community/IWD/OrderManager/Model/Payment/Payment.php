<?php

/**
 * Class IWD_OrderManager_Model_Payment_Payment
 */
class IWD_OrderManager_Model_Payment_Payment extends Mage_Core_Model_Abstract
{
    const IS_REAUTHORIZATION_ENABLED = true;

    protected $params;

    protected $_realTransactionIdKey = 'real_transaction_id';

    /**
     * @param $params
     */
    protected function init($params)
    {
        if (!isset($params['order_id'])) {
            Mage::throwException("Order id is not defined");
        }

        $this->params = $params;
    }

    /**
     * @param $params
     */
    public function updateOrderPayment($params)
    {
        $this->init($params);

        if (isset($params['confirm_edit']) && !empty($params['confirm_edit'])) {
            $this->addChangesToConfirm();
        } else {
            $this->editPaymentMethod($params['payment'], $params['order_id']);
            $this->addChangesToLog();
            $this->notifyEmail();
        }
    }

    /**
     * @param $paymentData
     * @param $orderId
     * @return bool
     */
    public function execUpdatePaymentMethod($paymentData, $orderId)
    {
        $this->editPaymentMethod($paymentData, $orderId);
        $this->notifyEmail();
        return true;
    }

    /**
     * @param $paymentData
     * @param $orderId
     * @return int
     */
    protected function editPaymentMethod($paymentData, $orderId)
    {
        try {
            if ($orderId) {
                $order = Mage::getModel('sales/order')->load($orderId);

                if (!empty($order) && $order->getEntityId()) {
                    $oldPayment = $order->getPayment()->getMethodInstance()->getTitle();

                    if ($order->getPayment()->getMethod() == "iwd_authorizecim") {
                        $transactions = Mage::getModel('sales/order_payment_transaction')->getCollection()
                            ->addAttributeToFilter('order_id', array('eq' => $order->getEntityId()))
                            ->addAttributeToFilter('txn_type', array('eq' => Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH));

                        $cardId = $order->getPayment()->getIwdAuthorizecimCardId();
                        $method = $order->getPayment()->getMethodInstance();
                        $card = $method->loadCard($cardId);
                        $gateway = $method->gateway();
                        $gateway->setCard($card);

                        foreach ($transactions as $transaction) {
                            $txnId = $transaction->getTxnId();
                            $delimiter = strpos($txnId, "-");
                            $txnId = $delimiter ? substr($txnId, 0, $delimiter) : $txnId;

                            $gateway->void($order->getPayment(), $txnId);
                            $transaction->setOrderPaymentObject($order->getPayment());
                            $transaction->close(false)->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID)->save();
                        }
                    }

                    $payment = $order->getPayment();
                    $payment->addData($paymentData)->save();
                    $method = $payment->getMethodInstance();
                    if ($order->getPayment()->getMethod() == 'braintree') {
                        $objData = new Varien_Object();
                        $objData->setData($paymentData);
                        $method->prepareSave()->assignData($objData);
                    } else {
                        $method->prepareSave()->assignData($paymentData);
                    }


                    $order->getPayment()->save();
                    $order->getPayment()->getOrder()->save();

                    $order = Mage::getModel('sales/order')->load($orderId);
                    $payment = $order->getPayment();
                    $payment->addData($paymentData)->save();
                    $payment->unsMethodInstance();
                    $method = $payment->getMethodInstance();
                    if ($order->getPayment()->getMethod() == 'braintree') {
                        $objData = new Varien_Object();
                        $objData->setData($paymentData);
                        $method->prepareSave()->assignData($objData);
                    } else {
                        $method->prepareSave()->assignData($paymentData);
                    }

                    if ($order->getPayment()->getMethod() == "iwd_authorizecim") {
                        $cardId = $order->getPayment()->getIwdAuthorizecimCardId();
                        $order->place();
                        $order->getPayment()->setIwdAuthorizecimCardId($cardId);
                    } else {
                        $order->place();
                    }

                    $order->getPayment()->save();
                    $order->getPayment()->getOrder()->save();
                    $newPayment = Mage::getModel('sales/order')->load($orderId)
                        ->getPayment()
                        ->getMethodInstance()
                        ->getTitle();

                    $quote = $order->getQuote();
                    $quote->getPayment()->setMethod($order->getPayment()->getMethod());
                    $quote->save();

                    Mage::getSingleton('iwd_ordermanager/logger')
                        ->addChangesToLog("payment_method", $oldPayment, $newPayment);

                    return 1;
                }
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addNotice($e->getMessage());
            IWD_OrderManager_Model_Logger::log($e->getMessage());
            return -1;
        }

        return 0;
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
        $logger = Mage::getSingleton('iwd_ordermanager/logger');
        $orderId = $this->params['order_id'];

        $logger->addCommentToOrderHistory($orderId);
        $logger->addLogToLogTable(IWD_OrderManager_Model_Confirm_Options_Type::PAYMENT, $orderId);
    }

    protected function addChangesToConfirm()
    {
        $this->estimatePaymentMethod($this->params['order_id'], $this->params['payment']);

        Mage::getSingleton('iwd_ordermanager/logger')->addLogToLogTable(
            IWD_OrderManager_Model_Confirm_Options_Type::PAYMENT,
            $this->params['order_id'],
            $this->params
        );

        $message = Mage::helper('iwd_ordermanager')->__(
            'Order update not yet applied. Customer has been sent an email with a confirmation link. Updates will be applied after confirmation.'
        );

        Mage::getSingleton('adminhtml/session')->addNotice($message);
    }


    /**
     * @return mixed
     */
    public function isAllowEditPayment()
    {
        return Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/order/actions/edit_payment');
    }

    /**
     * @param $orderId
     * @param $oldOrder
     * @return int
     */
    public function reauthorizePayment($orderId, $oldOrder)
    {
        if (self::IS_REAUTHORIZATION_ENABLED == false) {
            return 1;
        }

        try {
            $order = Mage::getModel('sales/order')->load($orderId);
            $payment = $order->getPayment();
            $orderMethod = $payment->getMethod();

            $oldAmountAuthorize = $payment->getBaseAmountAuthorized();
            $amount = $order->getBaseGrandTotal();

            /**
             * Authorized (but do not captured) more then we need now (authorized $100, need $80)
             */
            if (!$order->hasInvoices() && $oldAmountAuthorize >= $amount) {
                return 1;
            }

            switch ($orderMethod) {
                /**
                 * Offline payment methods
                 */
                case 'free':
                case 'checkmo':
                case 'purchaseorder':
                    return 1;

				# 2024-02-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
				# 1) "Delete the unused `Mage_Paygate` module":
				#  https://github.com/thehcginstitute-com/m1/issues/354
				# 2) "Delete the unused `Mage_Authorizenet` module":
				#  https://github.com/thehcginstitute-com/m1/issues/352

				# 2024-02-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
				# "Delete the unused `Mage_Paypal` module": https://github.com/thehcginstitute-com/m1/issues/356

				# 2024-02-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
				# 1) "Delete the unused `Mage_Paygate` module":
				#  https://github.com/thehcginstitute-com/m1/issues/354
				# 2) "Delete the unused `Mage_Authorizenet` module":
				#  https://github.com/thehcginstitute-com/m1/issues/352

                /**
                 * Another payments
                 */
                default:
                    return 1;
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iwd_ordermanager')->__($e->getMessage()));
            IWD_OrderManager_Model_Logger::log($e->getMessage(), true);
            return -1;
        }
    }

	# 2024-02-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# 1) "Delete the unused `Mage_Paygate` module": https://github.com/thehcginstitute-com/m1/issues/354
	# 2) "Delete the unused `Mage_Authorizenet` module": https://github.com/thehcginstitute-com/m1/issues/352

    /**
     * @param $payment
     */
    protected function savePayment($payment)
    {
        Mage::unregister('edited_order_id');
        $payment->getOrder()->save();
        $payment->save();
    }

	# 2024-02-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# 1) "Delete the unused `Mage_Paygate` module": https://github.com/thehcginstitute-com/m1/issues/354
	# 2) "Delete the unused `Mage_Authorizenet` module": https://github.com/thehcginstitute-com/m1/issues/352

	# 2024-02-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# "Delete the unused `Mage_Paypal` module": https://github.com/thehcginstitute-com/m1/issues/356


    /**
     * @return array
     */
    public function GetActivePaymentMethods()
    {
        $payments = Mage::getModel('payment/config')->getActiveMethods();
        return $this->getMethodsTitle($payments);
    }

    /**
     * @return array
     */
    public function getActivePaymentMethodsArray()
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        $methods = array(array('value'=>'', 'label'=>Mage::helper('adminhtml')->__('--Please Select--')));
        foreach ($payments as $paymentCode=>$paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = array(
                'label'   => $paymentTitle,
                'value' => $paymentCode,
            );
        }

        return $methods;
    }

    /**
     * @return array
     */
    public function GetAllPaymentMethods()
    {
        $payments = Mage::getModel('payment/config')->getAllMethods();
        return $this->getMethodsTitle($payments);
    }

    /**
     * @param $payments
     * @return array
     */
    protected function getMethodsTitle($payments)
    {
        $methods = array();

        foreach ($payments as $paymentCode => $paymentModel) {
            $methods[$paymentCode] = Mage::getStoreConfig('payment/' . $paymentCode . '/title');
        }

        return $methods;
    }

    /**
     * @return array
     */
    public function GetPaymentMethods()
    {
        $resource = Mage::getSingleton('core/resource')->getConnection('core_read');
        $tableName = Mage::getSingleton('core/resource')->getTableName('sales/order_payment');
        $results = $resource->fetchAll("SELECT DISTINCT `method` FROM `{$tableName}`");

        $methods = array();

        foreach ($results as $paymentCode) {
            $code = $paymentCode['method'];
            $methods[$code] = Mage::getStoreConfig('payment/' . $code . '/title');
        }

        return $methods;
    }

    /**
     * @param $orderId
     * @return bool
     */
    public function canUpdatePaymentMethod($orderId)
    {
        $order = Mage::getModel('sales/order')->load($orderId);
        if (empty($order)) {
            return false;
        }

        return !$order->hasInvoices();
    }

    /**
     * @param $orderId
     * @param $paymentData
     */
    public function estimatePaymentMethod($orderId, $paymentData)
    {
        $order = Mage::getModel('sales/order')->load($orderId);

        $oldPayment = $order->getPayment()->getMethodInstance()->getTitle();
        $newPayment = Mage::helper('payment')->getMethodInstance($paymentData['method'])->getTitle();

        $totals = array(
            'grand_total' => $order->getGrandTotal(),
            'base_grand_total' => $order->getBaseGrandTotal(),
        );

        $log = Mage::getSingleton('iwd_ordermanager/logger');
        $log->addNewTotalsToLog($totals);
        $log->addChangesToLog("payment_method", $oldPayment, $newPayment);
        $log->addCommentToOrderHistory($orderId, 'wait');
    }

	# 2024-02-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# 1) "Delete the unused `Mage_Paygate` module": https://github.com/thehcginstitute-com/m1/issues/354
	# 2) "Delete the unused `Mage_Authorizenet` module": https://github.com/thehcginstitute-com/m1/issues/352
}