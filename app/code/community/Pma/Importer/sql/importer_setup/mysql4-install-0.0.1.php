<?php

  $installer = $this;
  $installer->startSetup();

        $setup = Mage::getModel('customer/entity_setup', 'core_setup');
        $setup->addAttribute('customer', 'visitorid', array(
                                                                'type' => 'varchar',
                                                                'input' => 'text',
                                                                'label' => 'Visitor Id',
                                                                'global' => 1,
                                                                'visible' => 1,
                                                                'required' => 0,
                                                                'user_defined' => 1,
                                                                'default' => '0',
                                                                'visible_on_front' => 1,
                                                                'source'=> 'profile/entity_visitorid'
                                                            ));
            
        if (version_compare(Mage::getVersion(), '1.6.0', '<='))
        {
              $customer = Mage::getModel('customer/customer');
              $attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
              $setup->addAttributeToSet('customer', $attrSetId, 'General', 'visitorid');
              
            $admincustomer = Mage::getModel('customer/form');
            $admincustomer->addAttribute('customer', 'visitorid', array(
                                                                'type' => 'varchar',
                                                                'input' => 'text',
                                                                'label' => 'Visitor Id',
                                                                'global' => 1,
                                                                'visible' => 1,
                                                                'required' => 0,
                                                                'user_defined' => 1,
                                                                'default' => '0',
                                                                'visible_on_front' => 1,
                                                                'source'=> 'profile/entity_visitorid'
                                                            ));
              
        }
        
        if (version_compare(Mage::getVersion(), '1.4.2', '>='))
        {
            Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'visitorid')
            ->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit','checkout_register','adminhtml_checkout','sales_order_create_form'))
            ->save();
        }
        
$sql = <<<SQLTEXT
CREATE TABLE IF NOT EXISTS {$this->getTable('importer_setting')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webservice_url` text NOT NULL,
  `account_token` text NOT NULL,
  `updated_on` datetime NOT NULL,
  `orders_type` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

SQLTEXT;

$installer->run($sql);
$installer->endSetup();
