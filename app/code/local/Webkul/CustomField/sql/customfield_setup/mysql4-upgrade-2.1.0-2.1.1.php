<?php
$installer = $this;
$installer->startSetup();
$installer->getConnection()
->addColumn($installer->getTable('wk_customerfields'),
'dependent_on', 
array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'nullable'  => true,
    'length'    => 255,
    'after'     => null,
    'default'   => NULL,
    'comment'   => 'dependent on?'
    )
); 
$installer->endSetup();