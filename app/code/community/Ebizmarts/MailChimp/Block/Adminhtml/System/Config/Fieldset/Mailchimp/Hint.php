<?php
/**
 * MailChimp For Magento
 *
 * @category  Ebizmarts_MailChimp
 * @author    Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date:     4/29/16 3:55 PM
 * @file:     Hint.php
 */
class Ebizmarts_MailChimp_Block_Adminhtml_System_Config_Fieldset_Mailchimp_Hint
    extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'ebizmarts/mailchimp/system/config/fieldset/hint.phtml';

    /**
     * Render fieldset html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->toHtml();
    }

    /**
     * @return string
     */
    function getMailChimpVersion()
    {
        return (string)Mage::getConfig()->getNode('modules/Ebizmarts_MailChimp/version');
    }

    /**
     * @return mixed
     */
    function getMigrationFinishedStatus()
    {
        return $this->makeMigrationHelper()->migrationFinished();
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->addJs('ebizmarts/mailchimp/config.js');
        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    function getStoresUrl()
    {
        return  Mage::helper('adminhtml')->getUrl('adminhtml/mailchimp/getStores');
    }

    /**
     * @return mixed
     */
    function getListUrl()
    {
        return  Mage::helper('adminhtml')->getUrl('adminhtml/mailchimp/getList');
    }

    /**
     * @return mixed
     */
    function getInfoUrl()
    {
        return  Mage::helper('adminhtml')->getUrl('adminhtml/mailchimp/getInfo');
    }

    /**
     * @return mixed
     */
    function getInterestUrl()
    {
        return  Mage::helper('adminhtml')->getUrl('adminhtml/mailchimp/getInterest');
    }

    function isApiKeySet()
    {
        $helper = $this->makeHelper();
        $scopeArray = $helper->getCurrentScope();
        $apikey = $helper->getApiKey($scopeArray['scope_id'], $scopeArray['scope']);
        if ($apikey !== null && $apikey !== '') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return Ebizmarts_MailChimp_Helper_Data
     */
    protected function makeHelper()
    {
        return Mage::helper('mailchimp');
    }

    /**
     * @return Ebizmarts_MailChimp_Helper_Migration
     */
    protected function makeMigrationHelper()
    {
        return Mage::helper('mailchimp/migration');
    }
}
