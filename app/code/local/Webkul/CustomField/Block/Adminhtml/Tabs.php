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

         /**
         * Defines after which tab, this tab should be rendered
         *
         * @return string
         */
        public function getAfter() {
            return 'tags';
        }

    }


