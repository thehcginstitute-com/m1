<?php
/**
 * Import model which collect all previous orders
 *
 * @author Shubhanshu Chouhan <shubhanshu@richpanel.com>
 */
class Richpanel_Analytics_Model_Import extends Mage_Core_Model_Abstract
{
    private $_ordersTotal = 0;
    private $_totalChunks = 0;
    private $_chunkItems  = 15;

    /**
     * Prepare all order ids
     *
     * @return void
     */
    public function _construct()
    {

    }

    /**
     * Get chunk orders
     *
     * @param  int
     * @return Varien_Data_Collection
     */
    public function getOrders($storeId, $chunkId)
    {
        return $this->_getOrderQuery($storeId)
                    ->setPageSize($this->_chunkItems)
                    ->setCurPage($chunkId + 1);
    }

    /**
     * Chenks array
     *
     * @return array
     */
    public function getChunks($storeId)
    {
        $storeTotal = $this->_getOrderQuery($storeId)->getSize();

        return (int)ceil($storeTotal / $this->_chunkItems);
    }

    /**
    * Get contextual store id
    *
    * @return int
    */
    public function getStoreId()
    {
        $helper  = Mage::helper('richpanel_analytics');
        $request = Mage::app()->getRequest();

        return $helper->getStoreId($request);
    }

    private function _getOrderQuery($storeId)
    {
        $now = Mage::getModel('core/date')->timestamp(time());
        $dateStart = date('Y-m-d' . ' 00:00:00', strtotime("-24 Months"));
        // $dateEnd = date('Y-m-d' . ' 23:59:59', $now);
        // $dateMinus12 = date("Y-m-d", strtotime("-24 Months"));
        // $date = date("Y-m-d");
        
        return Mage::getModel('sales/order')
                    ->getCollection()
                    ->addAttributeToFilter('store_id', $storeId)
                    ->addAttributeToFilter('created_at', 
                        array(
                            'from'=>$dateStart, 
                            // 'to'=>$date,
                            'date' => true
                        )
                    );
    }
}
