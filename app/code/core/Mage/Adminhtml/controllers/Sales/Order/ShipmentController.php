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
 * @copyright  Copyright (c) 2017-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order shipment controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Sales_Order_ShipmentController extends Mage_Adminhtml_Controller_Sales_Shipment
{
    /**
     * Initialize shipment items QTY
     */
    protected function _getItemQtys()
    {
        $data = $this->getRequest()->getParam('shipment');
        $qtys = $data['items'] ?? [];
        return $qtys;
    }

    /**
     * Initialize shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment|bool
     * @throws Mage_Core_Exception
     */
    protected function _initShipment()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Shipments'));

        $shipment = false;
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $orderId = $this->getRequest()->getParam('order_id');
        if ($shipmentId) {
            $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
            if (!$shipment->getId()) {
                $this->_getSession()->addError($this->__('The shipment no longer exists.'));
                return false;
            }
        } elseif ($orderId) {
            $order      = Mage::getModel('sales/order')->load($orderId);

            /**
             * Check order existing
             */
            if (!$order->getId()) {
                $this->_getSession()->addError($this->__('The order no longer exists.'));
                return false;
            }
            /**
             * Check shipment is available to create separate from invoice
             */
            if ($order->getForcedDoShipmentWithInvoice()) {
                $this->_getSession()->addError($this->__('Cannot do shipment for the order separately from invoice.'));
                return false;
            }
            /**
             * Check shipment create availability
             */
            if (!$order->canShip()) {
                $this->_getSession()->addError($this->__('Cannot do shipment for the order.'));
                return false;
            }
            $savedQtys = $this->_getItemQtys();
            $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);

            $tracks = $this->getRequest()->getPost('tracking');
            if ($tracks) {
                foreach ($tracks as $data) {
                    if (empty($data['number'])) {
                        Mage::throwException($this->__('Tracking number cannot be empty.'));
                    }
                    $track = Mage::getModel('sales/order_shipment_track')
                        ->addData($data);
                    $shipment->addTrack($track);
                }
            }
        }

        Mage::register('current_shipment', $shipment);
        return $shipment;
    }

    /**
     * Save shipment and order in one transaction
     *
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @return $this
     * @throws Exception
     */
    protected function _saveShipment($shipment)
    {
        $shipment->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($shipment)
            ->addObject($shipment->getOrder())
            ->save();

        return $this;
    }

    /**
     * Shipment information page
     */
    function viewAction()
    {
        $shipment = $this->_initShipment();
        if ($shipment) {
            $this->_title(sprintf("#%s", $shipment->getIncrementId()));

            $this->loadLayout();

            /** @var Mage_Adminhtml_Block_Sales_Order_Shipment_View $block */
            $block = $this->getLayout()->getBlock('sales_shipment_view');
            $block->updateBackButtonUrl($this->getRequest()->getParam('come_from'));

            $this->_setActiveMenu('sales/order')
                ->renderLayout();
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Start create shipment action
     */
    function startAction()
    {
        /**
         * Clear old values for shipment qty's
         */
        $this->_redirect('*/*/new', ['order_id' => $this->getRequest()->getParam('order_id')]);
    }

    /**
     * Shipment create page
     */
    function newAction()
    {
        if ($shipment = $this->_initShipment()) {
            $this->_title($this->__('New Shipment'));

            $comment = Mage::getSingleton('adminhtml/session')->getCommentText(true);
            if ($comment) {
                $shipment->setCommentText($comment);
            }

            $this->loadLayout()
                ->_setActiveMenu('sales/order')
                ->renderLayout();
        } else {
            $this->_redirect('*/sales_order/view', ['order_id' => $this->getRequest()->getParam('order_id')]);
        }
    }

    /**
     * Save shipment
     * We can save only new shipment. Existing shipments are not editable
     */
    function saveAction()
    {
        $data = $this->getRequest()->getPost('shipment');
        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }

        $shipment = $this->_initShipment();
        if (!$shipment) {
            $this->_forward('noRoute');
            return;
        }

        $responseAjax = new Varien_Object();
		# 2024-02-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Delete the shipping labels feature because it is unused": https://github.com/thehcginstitute-com/m1/issues/375

        try {
            $shipment->register();
            $comment = '';
            if (!empty($data['comment_text'])) {
                $shipment->addComment(
                    $data['comment_text'],
                    isset($data['comment_customer_notify']),
                    isset($data['is_visible_on_front'])
                );
                if (isset($data['comment_customer_notify'])) {
                    $comment = $data['comment_text'];
                }
            }

            if (!empty($data['send_email'])) {
                $shipment->setEmailSent(true);
            }

            $shipment->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));

			# 2024-02-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# "Delete the shipping labels feature because it is unused":
			#https://github.com/thehcginstitute-com/m1/issues/375

            $this->_saveShipment($shipment);

            $shipment->sendEmail(!empty($data['send_email']), $comment);

            $shipmentCreatedMessage = $this->__('The shipment has been created.');
			# 2024-02-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# "Delete the shipping labels feature because it is unused":
			# https://github.com/thehcginstitute-com/m1/issues/375
            $this->_getSession()->addSuccess($shipmentCreatedMessage);
            Mage::getSingleton('adminhtml/session')->getCommentText(true);
        } catch (Mage_Core_Exception $e) {
				# 2024-02-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
				# "Delete the shipping labels feature because it is unused":
				# https://github.com/thehcginstitute-com/m1/issues/375
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/new', ['order_id' => $this->getRequest()->getParam('order_id')]);
        } catch (Exception $e) {
            Mage::logException($e);
			# 2024-02-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# "Delete the shipping labels feature because it is unused":
			# https://github.com/thehcginstitute-com/m1/issues/375
			$this->_getSession()->addError($this->__('Cannot save shipment.'));
			$this->_redirect('*/*/new', ['order_id' => $this->getRequest()->getParam('order_id')]);
        }
		# 2024-02-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Delete the shipping labels feature because it is unused":
		# https://github.com/thehcginstitute-com/m1/issues/375
        $this->_redirect('*/sales_order/view', ['order_id' => $shipment->getOrderId()]);
    }

    /**
     * Send email with shipment data to customer
     */
    function emailAction()
    {
        try {
            $shipment = $this->_initShipment();
            if ($shipment) {
                $shipment->sendEmail(true)
                    ->setEmailSent(true)
                    ->save();
                $historyItem = Mage::getResourceModel('sales/order_status_history_collection')
                    ->getUnnotifiedForInstance($shipment, Mage_Sales_Model_Order_Shipment::HISTORY_ENTITY_NAME);
                if ($historyItem) {
                    $historyItem->setIsCustomerNotified(1);
                    $historyItem->save();
                }
                $this->_getSession()->addSuccess($this->__('The shipment has been sent.'));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot send shipment information.'));
        }
        $this->_redirect('*/*/view', [
            'shipment_id' => $this->getRequest()->getParam('shipment_id')
        ]);
    }

    /**
     * Add new tracking number action
     */
    function addTrackAction()
    {
        try {
            $carrier = $this->getRequest()->getPost('carrier');
            $number  = $this->getRequest()->getPost('number');
            $title  = $this->getRequest()->getPost('title');
            if (empty($carrier)) {
                Mage::throwException($this->__('The carrier needs to be specified.'));
            }
            if (empty($number)) {
                Mage::throwException($this->__('Tracking number cannot be empty.'));
            }
            $shipment = $this->_initShipment();
            if ($shipment) {
                $track = Mage::getModel('sales/order_shipment_track')
                    ->setNumber($number)
                    ->setCarrierCode($carrier)
                    ->setTitle($title);
                $shipment->addTrack($track)
                    ->save();

                $this->loadLayout();
                $response = $this->getLayout()->getBlock('shipment_tracking')->toHtml();
            } else {
                $response = [
                    'error'     => true,
                    'message'   => $this->__('Cannot initialize shipment for adding tracking number.'),
                ];
            }
        } catch (Mage_Core_Exception $e) {
            $response = [
                'error'     => true,
                'message'   => $e->getMessage(),
            ];
        } catch (Exception $e) {
            $response = [
                'error'     => true,
                'message'   => $this->__('Cannot add tracking number.'),
            ];
        }
        if (is_array($response)) {
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Remove tracking number from shipment
     */
    function removeTrackAction()
    {
        $trackId    = $this->getRequest()->getParam('track_id');
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $track = Mage::getModel('sales/order_shipment_track')->load($trackId);
        if ($track->getId()) {
            try {
                if ($this->_initShipment()) {
                    $track->delete();

                    $this->loadLayout();
                    $response = $this->getLayout()->getBlock('shipment_tracking')->toHtml();
                } else {
                    $response = [
                        'error'     => true,
                        'message'   => $this->__('Cannot initialize shipment for delete tracking number.'),
                    ];
                }
            } catch (Exception $e) {
                $response = [
                    'error'     => true,
                    'message'   => $this->__('Cannot delete tracking number.'),
                ];
            }
        } else {
            $response = [
                'error'     => true,
                'message'   => $this->__('Cannot load track with retrieving identifier.'),
            ];
        }
        if (is_array($response)) {
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * View shipment tracking information
     */
    function viewTrackAction()
    {
        $trackId    = $this->getRequest()->getParam('track_id');
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $track = Mage::getModel('sales/order_shipment_track')->load($trackId);
        if ($track->getId()) {
            try {
                $response = $track->getNumberDetail();
            } catch (Exception $e) {
                $response = [
                    'error'     => true,
                    'message'   => $this->__('Cannot retrieve tracking number detail.'),
                ];
            }
        } else {
            $response = [
                'error'     => true,
                'message'   => $this->__('Cannot load track with retrieving identifier.'),
            ];
        }

        if (is_object($response)) {
            $className = Mage::getConfig()->getBlockClassName('adminhtml/template');
            $block = new $className();
            $block->setType('adminhtml/template')
                ->setIsAnonymous(true)
                ->setTemplate('sales/order/shipment/tracking/info.phtml');

            $block->setTrackingInfo($response);

            $this->getResponse()->setBody($block->toHtml());
        } else {
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
            }

            $this->getResponse()->setBody($response);
        }
    }

    /**
     * Add comment to shipment history
     */
    function addCommentAction()
    {
        try {
            $this->getRequest()->setParam(
                'shipment_id',
                $this->getRequest()->getParam('id')
            );
            $data = $this->getRequest()->getPost('comment');
            if (empty($data['comment'])) {
                Mage::throwException($this->__('Comment text field cannot be empty.'));
            }
            $shipment = $this->_initShipment();
            $shipment->addComment(
                $data['comment'],
                isset($data['is_customer_notified']),
                isset($data['is_visible_on_front'])
            );
            $shipment->sendUpdateEmail(!empty($data['is_customer_notified']), $data['comment']);
            $shipment->save();

            $this->loadLayout(false);
            $response = $this->getLayout()->getBlock('shipment_comments')->toHtml();
        } catch (Mage_Core_Exception $e) {
            $response = [
                'error'     => true,
                'message'   => $e->getMessage()
            ];
            $response = Mage::helper('core')->jsonEncode($response);
        } catch (Exception $e) {
            $response = [
                'error'     => true,
                'message'   => $this->__('Cannot add new comment.')
            ];
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

	# 2024-02-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# "Delete the shipping labels feature because it is unused": https://github.com/thehcginstitute-com/m1/issues/375

	# 2024-02-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# "Delete the shipment packaging feature because it is unused": https://github.com/thehcginstitute-com/m1/issues/376
}
