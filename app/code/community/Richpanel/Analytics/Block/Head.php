<?php
/**
 * Block for head part who render all js lines
 *
 * @author Shubhanshu Chouhan <shubhanshu@richpanel.com>
 */
class Richpanel_Analytics_Block_Head extends Mage_Core_Block_Template
{
    /**
     * key in session storage
     */
    const DATA_TAG = "richpanel_events";

    /**
     * Get events to track them to richpanel js api
     *
     * @return array
     */
    public function getEvents()
    {
        $events = (array)Mage::getSingleton('core/session')->getData(self::DATA_TAG);
        // clear events from session ater get events once
        Mage::getSingleton('core/session')->setData(self::DATA_TAG,'');
        return array_filter($events);
    }

    /**
     * Render richpanel js if module is enabled
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        $helper = Mage::helper('richpanel_analytics');

        $request = Mage::app()->getRequest();
        $storeId = $helper->getStoreId($request);

        if($helper->isEnabled($storeId)) {
            return $html;
        }

        return "";
    }
}
