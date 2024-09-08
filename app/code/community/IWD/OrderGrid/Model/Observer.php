<?php

/**
 * Class IWD_OrderGrid_Model_Observer
 */
class IWD_OrderGrid_Model_Observer
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

    /**
     * @param Varien_Event_Observer $observer
     */
    function addMassactionsToOrderGrid(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();

        $isInvoiceAllowed =
            Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/invoice');
        $isShipAllowed =
            Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/ship');

        if ($block->getId() == 'sales_order_grid' && Mage::helper('iwd_ordergrid')->isEnabled()) {
            $helper = Mage::helper('adminhtml');
            $massactionBlock = $block->getMassactionBlock();
            if ($massactionBlock) {

                /** @var $sourceYesNo Mage_Adminhtml_Model_System_Config_Source_Yesno */
                $sourceYesNo = Mage::getSingleton('adminhtml/system_config_source_yesno');
                $yesNo = Mage::getStoreConfig(IWD_OrderGrid_Helper_Data::CONFIG_XPATH_NOTIFY_CUSTOMER_MASSACTION);
                $additional = array(
                    'notify' => array(
                        'name' => 'notify',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => $helper->__('Notify Customer'),
                        'values' => $sourceYesNo->toOptionArray(),
                        'value' => $yesNo
                    )
                );

                if ($isInvoiceAllowed) {

                    $massactionBlock->addItem(
                        'iwd_invoice',
                        array(
                            'label' => $helper->__('Invoice'),
                            'url' => $helper->getUrl(
                                '*/sales_bulk/create',
                                array('invoice' => 1, 'shipment' => 0, 'print' => 0)
                            ),
                            'additional' => $additional
                        )
                    );

                    $massactionBlock->addItem(
                        'iwd_invoice_print',
                        array(
                            'label' => $helper->__('Invoice + Print'),
                            'url' => $helper->getUrl(
                                '*/sales_bulk/create',
                                array('invoice' => 1, 'shipment' => 0, 'print' => 1)
                            ),
                            'additional' => $additional
                        )
                    );
                }

                if ($isShipAllowed) {

                    $massactionBlock->addItem(
                        'iwd_ship',
                        array(
                            'label' => $helper->__('Ship'),
                            'url' => $helper->getUrl(
                                '*/sales_bulk/create',
                                array('invoice' => 0, 'shipment' => 1, 'print' => 0)
                            ),
                            'additional' => $additional
                        )
                    );

                    $massactionBlock->addItem(
                        'iwd_ship_print',
                        array(
                            'label' => $helper->__('Ship + Print'),
                            'url' => $helper->getUrl(
                                '*/sales_bulk/create',
                                array('invoice' => 0, 'shipment' => 1, 'print' => 1)
                            ),
                            'additional' => $additional
                        )
                    );
                }

                if ($isShipAllowed && $isInvoiceAllowed) {

                    $massactionBlock->addItem(
                        'iwd_invoice_ship',
                        array(
                            'label' => $helper->__('Invoice + Ship'),
                            'url' => $helper->getUrl(
                                '*/sales_bulk/create',
                                array('invoice' => 1, 'shipment' => 1, 'print' => 0)
                            ),
                            'additional' => $additional
                        )
                    );

                    $massactionBlock->addItem(
                        'iwd_invoice_ship_print',
                        array(
                            'label' => $helper->__('Invoice + Ship + Print'),
                            'url' => $helper->getUrl(
                                '*/sales_bulk/create',
                                array('invoice' => 1, 'shipment' => 1, 'print' => 1)
                            ),
                            'additional' => $additional
                        )
                    );
                }

                $massactionBlock->addItem(
                    'iwd_resent_invoice',
                    array(
                        'label' => $helper->__('Re-send invoice email'),
                        'url' => $helper->getUrl('*/sales_bulk/resentInvoice', array('redirect' => 'sales_order'))
                    )
                );

                $massactionBlock->addItem(
                    'iwd_resent_shipment',
                    array(
                        'label' => $helper->__('Re-send shipment email'),
                        'url' => $helper->getUrl('*/sales_bulk/resentShipment', array('redirect' => 'sales_order'))
                    )
                );
            }
        }
    }
}
