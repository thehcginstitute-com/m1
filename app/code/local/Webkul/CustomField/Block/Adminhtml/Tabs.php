<?php
 /**
 * @category   Webkul
 * @package    Webkul_CustomField
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */ 
class Webkul_CustomField_Block_Adminhtml_Tabs extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
    {

        public function __construct() {
            $this->setTemplate('customfield/fieldvalues.phtml');
            
        }

        public function getCustomtabInfo() {
            $customer = Mage::registry('current_customer');
            $customtab='Custom Registration Fields';
            return $customtab;
        }

        /**
         * Return Tab label
         *
         * @return string
         */
        public function getTabLabel() {
            return $this->__('Custom Registration Fields');
        }

        /**
         * Return Tab title
         *
         * @return string
         */
        public function getTabTitle() {
            return $this->__('Custom Registration Fields');
        }

        /**
         * Can show tab in tabs
         *
         * @return boolean
         */
        public function canShowTab() {
            $customer = Mage::registry('current_customer');
            return (bool)$customer->getId();
        }

        /**
         * Tab is hidden
         *
         * @return boolean
         */
        public function isHidden() {
            return false;
        }

# 2024-02-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Delete the unused `Mage_Tag` module": https://github.com/thehcginstitute-com/m1/issues/372

    }


