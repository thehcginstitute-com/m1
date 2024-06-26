<?php
class Ebizmarts_MailChimp_Block_Adminhtml_Sales_Order_Grid_Renderer_MailchimpOrder
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    const SYNCED = 1;

    function render(Varien_Object $row)
    {
        $storeId = $row->getStoreId(); /** @var int $storeId */
        $orderId = $row->getEntityId();
        $orderDate = $row->getCreatedAt();
        $helper = $this->makeHelper();
        if ($helper->isEcomSyncDataEnabled($storeId)) {
            $resultArray = $this->makeApiOrders()->getSyncedOrder($orderId, hcg_mc_sid($storeId));
            $id = $resultArray['order_id'];
            $status = $resultArray['synced_status'];

            if ($status == self::SYNCED) {
                $result = '<div style ="color:green">' . $helper->__("Yes") . '</div>';
            } elseif ($status === null && $id !== null) {
                $result = '<div style ="color:#ed6502">' . $helper->__("Processing") . '</div>';
            } elseif ($status === null && $orderDate > $helper->getEcommerceFirstDate($storeId)) {
                $result = '<div style ="color:mediumblue">' . $helper->__("In queue") . '</div>';
            } else {
                $result = '<div style ="color:red">' . $helper->__("No") . '</div>';
            }
        } else {
            $result = '<div style ="color:red">' . $helper->__("No") . '</div>';
        }

        return $result;
    }

    /**
     * @return Ebizmarts_MailChimp_Helper_Data
     */
    protected function makeHelper() {return hcg_mc_h();}

    /**
     * @return Ebizmarts_MailChimp_Model_Api_Orders
     */
    protected function makeApiOrders()
    {
        return Mage::getModel('mailchimp/api_orders');
    }
}
