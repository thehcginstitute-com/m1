<?php

/**
 * Class IWD_OrderFlags_Model_Observer
 */
class IWD_OrderFlags_Model_Observer
{
    protected $isAdditionalColumnsAssigned = false;

    /************************ CHECK REQUIRED MODULES *************************/
    public function checkRequiredModules()
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

    /**
     * @param Varien_Event_Observer $observer
     */
    public function assignFlagsToOrder(Varien_Event_Observer $observer)
    {
        /**
         * @var $order Mage_Sales_Model_Order
         */
        $order = $observer->getEvent()->getOrder();
        $orderId = $order->getId();

        $flags = $this->getFlags($order);
        $typeFlagRelation = $this->getTypeFlagRelation($flags);

        foreach ($typeFlagRelation as $typeId => $flagId) {
            Mage::getModel('iwd_orderflags/flags_orders')->addNewRelation($flagId, $orderId, $typeId);
        }
    }

    /**
     * @param $flags array
     * @return array
     */
    protected function getTypeFlagRelation($flags)
    {
        $typeFlagRelation = array();
        foreach ($flags as $flagId) {
            $types = IWD_OrderFlags_Model_Flags_Flags::getAssignTypesForFlag($flagId);
            foreach ($types as $typeId) {
                $typeFlagRelation[$typeId] = $flagId;
            }
        }

        return $typeFlagRelation;
    }

    /**
     * @param $order Mage_Sales_Model_Order
     * @return array
     */
    protected function getFlags($order)
    {
        $flagsStatus = Mage::getModel('iwd_orderflags/flags_autoapply')->getCollection()
            ->addFieldToFilter('apply_type', IWD_OrderFlags_Model_Flags_Autoapply::TYPE_ORDER_STATUS)
            ->addFieldToFilter('method_key', $order->getStatus())
            ->getColumnValues('flag_id');

        $flagsPayments = Mage::getModel('iwd_orderflags/flags_autoapply')->getCollection()
            ->addFieldToFilter('apply_type', IWD_OrderFlags_Model_Flags_Autoapply::TYPE_PAYMENT_METHOD)
            ->addFieldToFilter('method_key', $order->getPayment()->getMethod())
            ->getColumnValues('flag_id');

        $flagsShipping = Mage::getModel('iwd_orderflags/flags_autoapply')->getCollection()
            ->addFieldToFilter('apply_type', IWD_OrderFlags_Model_Flags_Autoapply::TYPE_SHIPPING_METHOD)
            ->addFieldToFilter('method_key', $order->getShippingMethod())
            ->getColumnValues('flag_id');

        $flagsStoreView = Mage::getModel('iwd_orderflags/flags_autoapply')->getCollection()
            ->addFieldToFilter('apply_type', IWD_OrderFlags_Model_Flags_Autoapply::TYPE_STORE_VIEW)
            ->addFieldToFilter('method_key', $order->getStoreId())
            ->getColumnValues('flag_id');

        $flags = array_merge($flagsStatus, $flagsPayments, $flagsShipping, $flagsStoreView);
        $flags = array_unique($flags);

        return $flags;
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function assignFlagAfterUpdateOrders(Varien_Event_Observer $observer)
    {
        /**
         * @var $order Mage_Sales_Model_Order
         */
        $order = $observer->getEvent()->getOrder();

        $data = array(
            'status' => IWD_OrderFlags_Model_Flags_Autoapply::TYPE_ORDER_STATUS,
            'store_id' => IWD_OrderFlags_Model_Flags_Autoapply::TYPE_STORE_VIEW,
            'shipping_method' => IWD_OrderFlags_Model_Flags_Autoapply::TYPE_SHIPPING_METHOD
        );

        foreach ($data as $orderKey => $type) {
            $oldData = $order->getOrigData($orderKey);
            $newData = $order->getData($orderKey);

            $this->reassignFlags($order, $oldData, $newData, $type);
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function assignFlagAfterUpdatePayment(Varien_Event_Observer $observer)
    {
        /**
         * @var $payment Mage_Sales_Model_Order_Payment
         */
        $payment = $observer->getEvent()->getPayment();

        /**
         * @var $order Mage_Sales_Model_Order
         */
        $order = $payment->getOrder();

        $this->reassignFlags(
            $order,
            $payment->getOrigData('method'),
            $payment->getData('method'),
            IWD_OrderFlags_Model_Flags_Autoapply::TYPE_PAYMENT_METHOD
        );
    }

    protected function reassignFlags($order, $oldData, $newData, $type)
    {
        if ($oldData != $newData) {
            $orderId = $order->getId();

            /* unset flags */
            $flags = Mage::getModel('iwd_orderflags/flags_autoapply')->getCollection()
                ->addFieldToFilter('apply_type', $type)
                ->addFieldToFilter('method_key', $oldData)
                ->getColumnValues('flag_id');
            $typeFlagRelation = $this->getTypeFlagRelation($flags);
            foreach ($typeFlagRelation as $typeId => $flagId) {
                Mage::getModel('iwd_orderflags/flags_orders')->unAssignFlags($orderId, $typeId);
            }

            /* set flags */
            $flags = Mage::getModel('iwd_orderflags/flags_autoapply')->getCollection()
                ->addFieldToFilter('apply_type', $type)
                ->addFieldToFilter('method_key', $newData)
                ->getColumnValues('flag_id');
            $typeFlagRelation = $this->getTypeFlagRelation($flags);
            foreach ($typeFlagRelation as $typeId => $flagId) {
                Mage::getModel('iwd_orderflags/flags_orders')->addNewRelation($flagId, $orderId, $typeId);
            }
        }
    }

    /************************** MASSACTION EVENT *****************************/
    /**
     * @param Varien_Event_Observer $observer
     */
    public function addActionIntoMassaction(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();

        if (isset($block) && is_object($block) && $block->getId() == 'sales_order_grid'
            && Mage::getSingleton('admin/session')->isAllowed('iwd_orderflags/assign_flags')) {
            $massactionBlock = $block->getMassactionBlock();
            if ($massactionBlock) {
                $helper = Mage::helper('iwd_orderflags');
                $flagTypes = Mage::getModel('iwd_orderflags/flags_types')->getCollection();
                foreach ($flagTypes as $type) {
                    if (!$type->isTypeActive()) {
                        continue;
                    }
                    $flags = $type->getAssignedFlags();
                    $flags[-1] = $helper->__('-- Unassign Label --');
                    ksort($flags);
                    $massactionBlock->addItem(
                        $type->getOrderGridId(),
                        array(
                            'label' => $helper->__('Assign Label - ') . $type->getName(),
                            'url' => Mage::helper('adminhtml')->getUrl(
                                '*/flags_order/massApplyFlag',
                                array('type_id' => $type->getId())
                            ),
                            'additional' => array(
                                'flag_id' => array(
                                    'name' => 'flag_id',
                                    'type' => 'select',
                                    'class' => 'required-entry',
                                    'label' => $helper->__('Select Label'),
                                    'values' => $flags
                                )
                            )
                        )
                    );
                }
            }
        }
    }
    /************************************************* end MASSACTION EVENT **/

    /**
     * @param Varien_Event_Observer $observer
     */
    public function appendColumnToOrderGrid(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_Grid && $block->getId() != NULL &&
            Mage::helper('iwd_orderflags')->isEnabled() && is_object($block) && $block->getId() == 'sales_order_grid') {
            $orderGridEnabled = Mage::helper('iwd_orderflags')->isIwdOrderGridEnabled();
            $flagTypes = Mage::getModel('iwd_orderflags/flags_types')->getCollection();

            foreach ($flagTypes as $type) {
                if (!$type->isTypeActive()) {
                    continue;
                }

                $orderGrid = $type->getOrderGridId();

                $columnParams = array(
                    'header' => $type->getName(),
                    'type' => 'options',
                    'index' => $orderGrid,
                    'filter_index' => $orderGrid . '.flag_id',
                    'renderer' => 'IWD_OrderFlags_Block_Adminhtml_Sales_Order_Grid_Renderer_Flags',
                    'options' => $type->getAssignedFlags(),
                    'column_css_class' => 'v-align',
                    'width' => '60px'
                );

                if ($orderGridEnabled) {
                    $columnParams['header_css_class'] = 'complex-filter-select';
                    $columnParams['filter_condition_callback'] = array(Mage::getModel('iwd_ordergrid/order_grid'), 'complexFilter');
                    $columnParams['type'] = 'iwd_multiselect';
                }

                /** @var $block Mage_Adminhtml_Block_Sales_Order_Grid */
                $block->addColumnAfter($orderGrid, $columnParams, $type->getColumnAfter());
            }
        }
    }

    /**
     * @param $observer
     */
    public function salesOrderGridCollectionLoadBefore($observer)
    {
        if (Mage::helper('iwd_orderflags')->isEnabled() && !$this->isAdditionalColumnsAssigned){
            $collection = $observer->getOrderGridCollection();
            $unions = $collection->getSelect()->getPart(Zend_Db_Select::UNION);
            if (count($unions)) {
                return;
            }

            $tableNameIwdOmFlagsOrders = Mage::getSingleton('core/resource')->getTableName('iwd_om_flags_orders');
            $flagTypes = Mage::getModel('iwd_orderflags/flags_types')->getCollection();
            foreach ($flagTypes as $type) {
                if (!$type->isTypeActive()) {
                    continue;
                }

                $orderGrid = $type->getOrderGridId();
                $collection->getSelect()->joinLeft(
                    array($orderGrid => $tableNameIwdOmFlagsOrders),
                    "main_table.entity_id = $orderGrid.order_id AND $orderGrid.type_id = {$type->getId()}",
                    array("{$type->getOrderGridId()}" => "flag_id")
                );
            }
            $this->isAdditionalColumnsAssigned = true;
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function addColumnsSettings(Varien_Event_Observer $observer)
    {
        if (Mage::helper('iwd_orderflags')->isEnabled()) {
            $object = $observer->getEvent()->getObject();
            $flagTypes = Mage::getModel('iwd_orderflags/flags_types')->getCollection();
            foreach ($flagTypes as $type) {
                $object->addColumnToOrderGridSettings(
                    $type->getOrderGridId(),
                    Mage::helper('iwd_orderflags')->__('Label - ') . $type->getName()
                );
            }
        }
    }
}
