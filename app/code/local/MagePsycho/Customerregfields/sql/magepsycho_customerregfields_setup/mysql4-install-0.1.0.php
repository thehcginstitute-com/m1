<?php
$installer = $this;
$installer->startSetup();
$helper = Mage::helper('magepsycho_customerregfields');

if ($helper->checkVersion('1.4.2', '>=')) {
    Mage::getSingleton('eav/config')
        ->getAttribute('customer', 'group_id')
        ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit'))
        ->save();
}

$installer->endSetup();