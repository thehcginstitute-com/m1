<?php
# 2024-04-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Add the `?v=<version>` suffix to the JavaScript files loaded in the Magento's backend
# from the `/js/` folder (similar to theme JavaScript and CSS files)":
# https://github.com/thehcginstitute-com/m1/issues/583
class Mage_Adminhtml_Block_Page_Head extends HCG_Page_Block_Html_Head
{
    /**
     * @return string
     */
    protected function _getUrlModelClass()
    {
        return 'adminhtml/url';
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string
     */
    function getFormKey()
    {
        return Mage::getSingleton('core/session')->getFormKey();
    }

    /**
     * Retrieve Timeout Delay from Config
     *
     * @return int
     * @since 19.4.18 / 20.0.16
     */
    function getLoadingTimeout()
    {
        return (int)Mage::getStoreConfig('admin/design/loading_timeout');
    }
}
