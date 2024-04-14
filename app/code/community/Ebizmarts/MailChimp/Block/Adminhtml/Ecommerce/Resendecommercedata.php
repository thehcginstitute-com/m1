<?php

class Ebizmarts_MailChimp_Block_Adminhtml_Ecommerce_Resendecommercedata
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_mode = 'resendecommercedata';
    function __construct()
    {
        $this->_controller = 'adminhtml_ecommerce';
        $this->_blockGroup = 'mailchimp';

        parent::__construct();
        $this->_removeButton("delete");
        $this->_removeButton("back");
        $this->_removeButton("reset");
    }

    function getHeaderText()
    {
        return 'Data to send';
    }
}
