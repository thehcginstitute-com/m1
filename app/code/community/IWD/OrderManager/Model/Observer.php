<?php

class IWD_OrderManager_Model_Observer
{
    /************************ CHECK REQUIRED MODULES *************************/
    function checkRequiredModules()
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            if (!Mage::getConfig()->getModuleConfig('IWD_All')->is('active', 'true')) {
                $message = 'Please setup IWD_ALL in order to finish <strong>IWD Order Manager</strong> installation.';
                $this->addMessage($message);
            } else {
                $version = Mage::getConfig()->getModuleConfig('IWD_All')->version;
                if (version_compare($version, "2.0.0", "<")) {
                    $message = 'Please update IWD_ALL extension because some features of <strong>IWD Order Manager</strong> can be not available.';
                    $this->addMessage($message);
                }
            }
        }
    }

    protected function addMessage($message)
    {
        $cache = Mage::app()->getCache();

        $iwdAllUrl = 'https://www.iwdagency.com/modules/iwd_all.tgz';
        $iwdUserGuideUrl = 'https://www.iwdagency.com/help/';

        $noticeMessage = 'Important: ' . $message . '<br />' .
            'Please download <a href="' . $iwdAllUrl . '" target="_blank">IWD_ALL</a> and set it up via Magento Connect.<br />' .
            'Please refer link to <a href="' .$iwdUserGuideUrl . '" target="_blank">installation guide</a>';

        if ($cache->load("iwd_order_manager") === false) {
            Mage::getSingleton('adminhtml/session')->addNotice($noticeMessage);
        }

        $cache->save('true', 'iwd_order_manager', array("iwd_order_manager"), $lifeTime = 5);
    }
    /******************************************* end CHECK REQUIRED MODULES **/


    /************************** MASSACTION EVENT *****************************/
    /**
     * @param Varien_Event_Observer $observer
     */
    function orderManagerObserver(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('iwd_ordermanager')->isOrderManagerEnabled()) {
            return;
        }

        $block = $observer->getEvent()->getBlock();

        if ($this->_orderPrint($block)
            | $this->_orderArchive($block)
            | $this->_orderDelete($block)
            | $this->_orderShowHide($block)
            | $this->_orderUpdateStatus($block)
            | $this->_orderComments($block)
        ) {
            return;
        }

        if ($this->_invoiceDelete($block)) {
            return;
        }

        if ($this->_shipmentDelete($block)) {
            return;
        }

        if ($this->_creditmemoDelete($block)) {
            return;
        }
    }
    /************************************************* end MASSACTION EVENT **/


    /****************************** DELETE ***********************************/
    /**
     * @param $block
     * @return bool
     */
    private function _orderDelete($block)
    {
        if (Mage::getModel('iwd_ordermanager/order')->isAllowDeleteOrders()) {
            $helper = Mage::helper('adminhtml');

            if ($block->getId() == 'sales_order_grid') {
                $massactionBlock = $block->getMassactionBlock();
                if ($massactionBlock) {
                    $massactionBlock->addItem(
                        'iwd_delete_sales',
                        array(
                            'label' => $helper->__('Delete Selected Order(s)'),
                            'url' => $helper->getUrl('*/sales_grid/delete', array('redirect' => 'sales_order')),
                            'confirm' => $helper->__('Are you sure to delete the selected sales order(s)?  (Related Invoices, Shipments & Credit memos will be deleted too!)')
                        )
                    );
                }

                return true;
            }

            if (get_class($block) == 'Mage_Adminhtml_Block_Sales_Order_View') {
                $orderId = $block->getRequest()->getParam('order_id');
                if (Mage::getModel('iwd_ordermanager/order')->load($orderId)->canDelete()) {
                    $block->addButton('delete', array(
                        'label' => $helper->__('Delete'),
                        'class' => 'delete',
                        'onclick' =>
                            'deleteConfirm(\'' . $helper->__('Are you sure to delete this order? (Related Invoices, Shipments & Credit memos will be deleted too!)') . '\', \'' .
                            $helper->getUrl('*/sales_grid/delete', array('order_ids' => $orderId, 'redirect' => 'sales_order')) . '\')'
                    ), -1, 110);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @param $block
     * @return bool
     */
    private function _invoiceDelete($block)
    {
        if (Mage::getModel('iwd_ordermanager/invoice')->isAllowDeleteInvoices()) {
            $helper = Mage::helper('adminhtml');

            if ($block->getId() == 'sales_invoice_grid') {
                $massactionBlock = $block->getMassactionBlock();
                if ($massactionBlock) {
                    $confirm = (Mage::getModel('iwd_ordermanager/invoice')->allowDeleteRelatedCreditMemo()) ?
                        $helper->__('Are you sure to delete the selected invoice(s)? Attention: All related credit memo(s) will be removed too!') :
                        $helper->__('Are you sure to delete the selected invoice(s)? Invoice(s) with related credit memo(s) will not be removed.');
                    $massactionBlock->addItem('iwd_delete_sales', array(
                        'label' => $helper->__('Delete selected invoice(s)'),
                        'url' => $helper->getUrl('*/sales_invoice/delete'),
                        'confirm' => $confirm,
                    ));
                }

                return true;
            }

            if (get_class($block) == 'Mage_Adminhtml_Block_Sales_Order_Invoice_View') {
                $invoiceId = $block->getRequest()->getParam('invoice_id');
                if (Mage::getModel('iwd_ordermanager/invoice')->load($invoiceId)->canDelete()) {
                    $block->addButton('delete', array(
                        'label' => $helper->__('Delete'),
                        'class' => 'delete',
                        'onclick' =>
                            'deleteConfirm(\'' . $helper->__('Are you sure to delete this invoice?') . '\', \'' .
                            $helper->getUrl('*/sales_invoice/delete', array('_current' => true, 'invoice_ids' => $invoiceId)) . '\')',
                    ), -1, 111);
                }
                return true;
            }
        }

        return false;
    }

    /**
     * @param $block
     * @return bool
     */
    private function _creditmemoDelete($block)
    {
        if (Mage::getModel('iwd_ordermanager/creditmemo')->isAllowDeleteCreditmemos()) {
            $helper = Mage::helper('adminhtml');

            if ($block->getId() == 'sales_creditmemo_grid') {
                $massactionBlock = $block->getMassactionBlock();
                if ($massactionBlock) {
                    $massactionBlock->addItem(
                        'iwd_delete_sales',
                        array(
                            'label' => $helper->__('Delete selected credit memo(s)'),
                            'url' => $helper->getUrl('*/sales_creditmemo/delete'),
                            'confirm' => $helper->__('Are you sure to delete the selected credit memo(s)?'),
                        )
                    );
                }

                return true;
            }

            if (get_class($block) == 'Mage_Adminhtml_Block_Sales_Order_Creditmemo_View') {
                $creditmemoId = $block->getRequest()->getParam('creditmemo_id');
                if (Mage::getModel('iwd_ordermanager/creditmemo')->load($creditmemoId)->canDelete()) {
                    $block->addButton(
                        'delete',
                        array(
                            'label' => $helper->__('Delete'),
                            'class' => 'delete',
                            'onclick' =>
                                'deleteConfirm(\'' . $helper->__('Are you sure to delete this credit memo?') . '\', \'' .
                                $helper->getUrl('*/sales_creditmemo/delete', array('_current' => true, 'creditmemo_ids' => $creditmemoId)) . '\')',
                        ), -1, 112
                    );
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @param $block
     * @return bool
     */
    private function _shipmentDelete($block)
    {
        if (Mage::getModel('iwd_ordermanager/shipment')->isAllowDeleteShipments()) {
            $helper = Mage::helper('adminhtml');

            if ($block->getId() == 'sales_shipment_grid') {
                $massactionBlock = $block->getMassactionBlock();
                if ($massactionBlock) {
                    $massactionBlock->addItem(
                        'iwd_delete_sales',
                        array(
                            'label' => $helper->__('Delete selected shipment(s)'),
                            'url' => $helper->getUrl('*/sales_shipment/delete'),
                            'confirm' => $helper->__('Are you sure to delete the selected shipment(s)?')
                        )
                    );
                }

                return true;
            }

            if (get_class($block) == 'Mage_Adminhtml_Block_Sales_Order_Shipment_View') {
                $shipmentId = $block->getRequest()->getParam('shipment_id');
                $block->addButton('delete', array(
                    'label' => $helper->__('Delete'),
                    'class' => 'delete',
                    'onclick' =>
                        'deleteConfirm(\'' . $helper->__('Are you sure to delete this shipment?') . '\', \'' .
                        $helper->getUrl('*/sales_shipment/delete', array('_current' => true, 'shipment_ids' => $shipmentId)) . '\')',
                ), -1, 113);

                return true;
            }
        }

        return false;
    }
    /*********************************************************** end DELETE **/


    /************************** UPDATE STATUS ********************************/
    /**
     * @param $block
     * @return bool
     */
    private function _orderUpdateStatus($block)
    {
        if (Mage::getModel('iwd_ordermanager/order')->isAllowChangeOrderStatus() && $block->getId() == 'sales_order_grid') {
            $massactionBlock = $block->getMassactionBlock();
            if ($massactionBlock) {
                $helper = Mage::helper('adminhtml');
                $massactionBlock->addItem('iwd_update_status', array(
                    'label' => $helper->__('Change status'),
                    'url' => Mage::helper('adminhtml')->getUrl('*/sales_grid/changestatus', array('redirect' => 'sales_order')),
                    'confirm' => $helper->__('Are you sure to change status for the selected order(s)?'),
                    'additional' => array(
                        'visibility' => array(
                            'name' => 'status',
                            'type' => 'select',
                            'class' => 'required-entry',
                            'label' => $helper->__('Status'),
                            'values' => Mage::getSingleton('sales/order_config')->getStatuses()
                        )
                    )
                ));
            }

            return true;
        }

        return false;
    }
    /**************************************************** end UPDATE STATUS **/


    /****************************** ARCHIVE **********************************/
    /**
     * @param $block
     * @return bool
     */
    private function _orderArchive($block)
    {
        if (Mage::helper('iwd_ordermanager')->isEnterpriseMagentoEdition()) {
            return false;
        }

        if (Mage::getModel('iwd_ordermanager/archive')->isAllowArchiveOrders() && $block->getId() == 'sales_order_grid') {
            $massactionBlock = $block->getMassactionBlock();
            $helper = Mage::helper('adminhtml');
            if ($massactionBlock) {
                $massactionBlock->addItem('iwd_archive_orders', array(
                    'label' => $helper->__('Archive Selected Order(s)'),
                    'url' => $helper->getUrl('*/sales_archive_order/archive'),
                    'confirm' => $helper->__('Do you really want to archive these orders?'),
                ));
            }

            return true;
        }

        return false;
    }
    /********************************************************** end ARCHIVE **/

# 2024-02-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Delete the unused «Reauthorize» feature from IWD Order Manager": https://github.com/thehcginstitute-com/m1/issues/362

    /****************************** PRINT BUTTON *****************************/
    /**
     * @param $block
     * @return bool
     */
    private function _orderPrint($block)
    {
        $helper = Mage::helper('adminhtml');

        if (get_class($block) == 'Mage_Adminhtml_Block_Sales_Order_View') {
            $orderId = $block->getRequest()->getParam('order_id');
            $block->addButton('print', array(
                'label' => $helper->__('Print'),
                'class' => 'save',
                'onclick' => 'setLocation(\'' . $helper->getUrl('*/sales_orderr/print', array('order_id' => $orderId)) . '\')',
            ), -1, 115);
            return true;
        }

        if ($block->getId() == 'sales_order_grid') {
            $massactionBlock = $block->getMassactionBlock();
            if ($massactionBlock) {
                $massactionBlock->addItem(
                    'iwd_print_order',
                    array(
                        'label' => $helper->__('Print Order(s)'),
                        'url' => $helper->getUrl('*/sales_orderr/pdforders', array('redirect' => 'sales_order'))
                    )
                );
            }

            return true;
        }

        return false;
    }
    /********************************************************** end PRINT **/

    /**
     * @param $block
     * @return void
     */
    private function _orderComments($block)
    {
        if ($block->getId() == 'sales_order_grid') {
            $massactionBlock = $block->getMassactionBlock();
            if ($massactionBlock) {
                $helper = Mage::helper('iwd_ordermanager');

                /** @var $sourceYesNo Mage_Adminhtml_Model_System_Config_Source_Yesno */
                $sourceYesNo = Mage::getSingleton('adminhtml/system_config_source_yesno');

                $massactionBlock->addItem(
                    'iwd_order_comments',
                    array(
                        'label' => $helper->__('Add Comment To Order(s)'),
                        'url' => Mage::helper('adminhtml')->getUrl('*/sales_grid/orderComments'),
                        'additional' => array(
                            'iwd_om_visible' => array(
                                'name' => 'iwd_om_visible',
                                'type' => 'select',
                                'class' => 'required-entry',
                                'label' => $helper->__('Visible on Front'),
                                'values' => $sourceYesNo->toOptionArray()
                            ),
                            'iwd_om_notified' => array(
                                'name' => 'iwd_om_notified',
                                'type' => 'select',
                                'class' => 'required-entry',
                                'label' => $helper->__('Customer Notified'),
                                'values' => $sourceYesNo->toOptionArray()
                            ),
                            'iwd_om_comment' => array(
                                'name' => 'iwd_om_comment',
                                'type' => 'textarea',
                                'class' => 'input-text required-entry iwd_order_massaction_comments',
                                'label' => $helper->__('Comment'),
                            )
                        )
                    )
                );
            }
        }
    }

    /*********************** DELETE: CREATE BACKUPS **************************/
    /**
     * @param Varien_Event_Observer $observer
     * @throws Exception
     */
    function orderDelete(Varien_Event_Observer $observer)
    {
        $obj = $observer->getEvent()->getOrder();
		# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Delete the unused «Backup Sales» feature of `IWD_OrderManager`": https://github.com/thehcginstitute-com/m1/issues/412
        if (!Mage::helper('iwd_ordermanager')->isEnterpriseMagentoEdition()) {
            Mage::getModel('iwd_ordermanager/archive_order')->load($obj->getEntityId(), 'entity_id')->delete();
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     * @throws Exception
     */
    function invoiceDelete(Varien_Event_Observer $observer)
    {
        $obj = $observer->getEvent()->getInvoice();
		# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Delete the unused «Backup Sales» feature of `IWD_OrderManager`": https://github.com/thehcginstitute-com/m1/issues/412
        if (!Mage::helper('iwd_ordermanager')->isEnterpriseMagentoEdition()) {
            Mage::getModel('iwd_ordermanager/archive_invoice')->load($obj->getEntityId(), 'entity_id')->delete();
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     * @throws Exception
     */
    function creditmemoDelete(Varien_Event_Observer $observer)
    {
        $obj = $observer->getEvent()->getCreditmemo();
		# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Delete the unused «Backup Sales» feature of `IWD_OrderManager`": https://github.com/thehcginstitute-com/m1/issues/412
        if (!Mage::helper('iwd_ordermanager')->isEnterpriseMagentoEdition()) {
            Mage::getModel('iwd_ordermanager/archive_creditmemo')->load($obj->getEntityId(), 'entity_id')->delete();
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     * @throws Exception
     */
    function shipmentDelete(Varien_Event_Observer $observer)
    {
        $obj = $observer->getEvent()->getShipment();
		# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Delete the unused «Backup Sales» feature of `IWD_OrderManager`": https://github.com/thehcginstitute-com/m1/issues/412
        if (!Mage::helper('iwd_ordermanager')->isEnterpriseMagentoEdition()) {
            Mage::getModel('iwd_ordermanager/archive_shipment')->load($obj->getEntityId(), 'entity_id')->delete();
        }
    }

	# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# "Delete the unused «Backup Sales» feature of `IWD_OrderManager`": https://github.com/thehcginstitute-com/m1/issues/412
    /******************************************* end DELETE: CREATE BACKUPS **/


    /**************************** CRON ARCHIVE ORDERS ************************/
    /**
     * @return void
     */
    function scheduledArchiveOrders()
    {
        try {
            if (!Mage::helper('iwd_ordermanager')->isEnterpriseMagentoEdition()) {
                Mage::getModel('iwd_ordermanager/archive')->addSalesToArchive();
            }
        } catch(Exception $e){
            Mage::log($e->getMessage(), null, 'iwd_om_archive.log');
        }
    }
    /********************************************** end CRON ARCHIVE ORDERS **/


    /**************************** AFTER UPDATE SALES *************************/
    /**
     * @param Varien_Event_Observer $observer
     */
    function salesOrderAfterUpdate(Varien_Event_Observer $observer)
    {
        try {
            if (Mage::helper('iwd_ordermanager')->isEnterpriseMagentoEdition()) {
                return;
            }

            $orderIds = $observer->getEvent()->getData("order_id");
            if (!is_array($orderIds)) {
                $orderIds = array($orderIds);
            }

            $archivedOrders = Mage::getModel('iwd_ordermanager/archive_order')->getCollection()
                ->addFieldToSelect('entity_id')
                ->addFieldToFilter('entity_id', array('in' => $orderIds));

            $archivedIds = array();
            foreach ($archivedOrders as $order) {
                $archivedIds[] = $order->getEntityId();
            }

            Mage::getModel('iwd_ordermanager/archive')->addSalesToArchiveByIds($archivedIds);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_om_archive.log');
        }
    }
    /********************************************** end AFTER UPDATE SALE **/


    /**************************** Show/Hide Orders *************************/
    /**
     * @param $block
     * @return bool
     */
    private function _orderShowHide($block)
    {
        if (Mage::helper('iwd_ordermanager')->isAllowHideOrders()) {
            $helper = Mage::helper('adminhtml');

            if ($block->getId() == 'sales_order_grid') {
                $massactionBlock = $block->getMassactionBlock();
                if ($massactionBlock) {
                    /** @var $sourceYesNo Mage_Adminhtml_Model_System_Config_Source_Yesno */
                    $sourceYesNo = Mage::getSingleton('adminhtml/system_config_source_yesno');
                    $massactionBlock->addItem(
                        'iwd_hide_order',
                        array(
                            'label' => $helper->__('Hide Order(s) On Front'),
                            'url' => $helper->getUrl('*/sales_grid/hide', array('redirect' => 'sales_order')),
                            'confirm' => $helper->__('Are you sure to show/hide the selected sales order(s) on frontend in customer account?'),
                            'additional' => array(
                                'visibility' => array(
                                    'name' => 'status',
                                    'type' => 'select',
                                    'class' => 'required-entry',
                                    'label' => $helper->__('Status'),
                                    'values' => $sourceYesNo->toOptionArray()
                                )
                            )
                        )
                    );
                }

                return true;
            }
        }

        return false;
    }
}
