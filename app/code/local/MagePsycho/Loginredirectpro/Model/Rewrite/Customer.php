<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Loginredirectpro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Loginredirectpro_Model_Rewrite_Customer extends Mage_Customer_Model_Customer
{

    /**
     * Send email with new account related information - rewrite
     *
     * @param string $type
     * @param string $backUrl
     * @param string $storeId
     * @throws Mage_Core_Exception
     * @return Mage_Customer_Model_Customer
     */
    function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0')
    {
        $helper	= Mage::helper('magepsycho_loginredirectpro');

        $types = array(
            'registered'   => self::XML_PATH_REGISTER_EMAIL_TEMPLATE, // welcome email, when confirmation is disabled
            'confirmed'    => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE, // welcome email, when confirmation is enabled
            'confirmation' => self::XML_PATH_CONFIRM_EMAIL_TEMPLATE, // email with confirmation link
        );
        if (!isset($types[$type])) {
            Mage::throwException(Mage::helper('customer')->__('Wrong transactional account email type'));
        }

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId($this->getSendemailStoreId());
        }

        $emailTemplate = Mage::getStoreConfig($types[$type], $storeId);
        if ($type == 'registered') {
            if ($newAccountEmailTemplate = $helper->getAccountTemplate()) {
                $emailTemplate = $newAccountEmailTemplate;
            }
        }

        Mage::getModel('core/email_template')
            ->setDesignConfig(array('area'=>'frontend', 'store'=>$storeId))
            ->sendTransactional(
                $emailTemplate,
                Mage::getStoreConfig(self::XML_PATH_REGISTER_EMAIL_IDENTITY, $storeId),
                $this->getEmail(),
                $this->getName(),
                array('customer' => $this, 'back_url' => $backUrl));

        $translate->setTranslateInline(true);

        return $this;
    }
}