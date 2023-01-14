<?php
$installer = $this;
$installer->startSetup();
$setup = Mage::getModel('customer/entity_setup', 'core_setup');
$setup->addAttribute('customer', 'account_activated', array(
    'type'             => 'int',
    'input'            => 'select',
    'label'            => 'Account Activated',
    'global'           => 1,
    'visible'          => 1,
    'required'         => 0,
    'user_defined'     => 1,
    'default'          => '0',
    'visible_on_front' => 0,
    'source'           => 'eav/entity_attribute_source_boolean',
));

$customer  = Mage::getModel('customer/customer');
$attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
$setup->addAttributeToSet('customer', $attrSetId, 'General', 'account_activated');

$helper = Mage::helper('magepsycho_storerestrictionpro');
if ($helper->checkVersion('1.4.2', '>=')) {
    Mage::getSingleton('eav/config')
        ->getAttribute('customer', 'account_activated')
        ->setData('used_in_forms', array('adminhtml_customer'))
        ->save();
}
$installer->endSetup();