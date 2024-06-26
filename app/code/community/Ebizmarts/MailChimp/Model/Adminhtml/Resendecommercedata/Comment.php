<?php

class Ebizmarts_MailChimp_Model_Adminhtml_Resendecommercedata_Comment
{
    /**
     * @var Ebizmarts_MailChimp_Helper_Data
     */
    protected $_mcHelper;

    function __construct()
    {
        $this->setMcHelper();
    }

    /**
     * @return Ebizmarts_MailChimp_Helper_Data
     */
    function getMcHelper()
    {
        return $this->_mcHelper;
    }

    /**
     * @param Ebizmarts_MailChimp_Helper_Data $mcHelper
     */
    function setMcHelper() {$this->_mcHelper = hcg_mc_h();}

    /**
     * @return string
     */
    function getCommentText()
    {
        $helper = $this->getMcHelper();
        $scopeArray = $helper->getCurrentScope();
        $scope = $scopeArray['scope'];

        if ($scope === "default"){
            $comment = $helper->__("This will resend the ecommerce data "
                ."for all Websites and Store Views.");
        } else {
            $websiteOrStoreViewScope = $this->_getScope($scopeArray);
            $comment = $helper->__("This will resend the ecommerce data "
                ."for %s only.", $websiteOrStoreViewScope);
        }

        return $comment;
    }

    /**
     * @param $scopeArray
     * @return string
     */
    protected function _getScope($scopeArray)
    {
        $scope = $scopeArray['scope'];
        if ($scope == "websites"){
            $result = "this Website";
        } else {
            $result = "this Store View";
        }

        return $result;
    }
}
