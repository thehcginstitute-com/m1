<?php
/**
 * mc-magento Magento Component
 *
 * @category  Ebizmarts
 * @package   mc-magento
 * @author    Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date:     5/27/16 1:02 PM
 * @file:     ResendSubscribers.php
 */
class Ebizmarts_MailChimp_Block_Adminhtml_System_Config_ResendSubscribers
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebizmarts/mailchimp/system/config/resendsubscribers.phtml');
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    public function getButtonHtml()
    {
        $helper = $this->makeHelper();
        $scopeArray = $helper->getCurrentScope();
        if ($helper->isSubscriptionEnabled($scopeArray['scope_id'], $scopeArray['scope'])
            || $scopeArray['scope_id'] == 0
        ) {
            $button = $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'id' => 'resendsubscribers_button',
                        'label' => $helper->__('Resend Subscribers'),
                        'onclick' => 'javascript:resendsubscribers(); return false;',
                        'title' => $helper->__('Send all subscribers again only for current scope')
                    )
                );

            return $button->toHtml();
        }
    }
    public function getAjaxCheckUrl()
    {
        $helper = $this->makeHelper();
        $scopeArray = $helper->getCurrentScope();
        return Mage::helper('adminhtml')->getUrl('adminhtml/mailchimp/resendSubscribers', $scopeArray);
    }

    /**
     * @return Ebizmarts_MailChimp_Helper_Data
     */
    protected function makeHelper()
    {
        return $this->helper('mailchimp');
    }
}
