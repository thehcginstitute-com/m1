<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Notfound
 */    
class Amasty_Notfound_Block_Adminhtml_Attempt_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('logGrid');
        $this->setDefaultSort('date');
    }
    
    protected function _prepareCollection()
    {
        $this->setCollection(Mage::getModel('amnotfound/attempt')->getCollection());
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $hlp =  Mage::helper('amnotfound'); 
        
        $this->addColumn('date', array(
            'header'    => $hlp->__('Date'),
            'index'     => 'date',
            'type'      => 'datetime', 
            'width'     => '170px',
            'gmtoffset' => true,
            'default'	=> ' ---- ',
        ));
        
        $this->addColumn('user', array(
            'header' => $hlp->__('Username'),
            'index'  => 'user',
            
        ));

        $this->addColumn('client_ip', array(
            'header' => $hlp->__('IP Address'),
            'index'  => 'client_ip',
            
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
  {
      return false;
  }

}