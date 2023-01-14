<?php
$installer = $this;
$installer->startSetup();
$helper = Mage::helper('magepsycho_customerregfields');

$setup = Mage::getModel('customer/entity_setup', 'core_setup');
$setup->addAttribute('customer', 'mp_group_code', array(
	'type'             => 'varchar',
	'input'            => 'text',
	'label'            => 'Group Code',
	'global'           => 1,
	'visible'          => 1,
	'required'         => 0,
	'user_defined'     => 1,
	'default'          => '',
	'visible_on_front' => 1,
	'source'           => null,
	'validate_rules'   => 'a:0:{}',
	'data'             => 'magepsycho_customerregfields/customer_attribute_data_groupcode',
));

$customer  = Mage::getModel('customer/customer');
$attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
$setup->addAttributeToSet('customer', $attrSetId, 'General', 'mp_group_code');


if ($helper->checkVersion('1.4.2', '>=')) {

	Mage::getSingleton('eav/config')
	    ->getAttribute('customer', 'mp_group_code')
	    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'checkout_register'))
	    ->save();
}

$installer->endSetup();