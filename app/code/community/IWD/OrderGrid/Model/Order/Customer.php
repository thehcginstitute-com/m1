<?php

class IWD_OrderGrid_Model_Order_Customer extends Mage_Core_Model_Abstract
{
    protected $params;

    protected $customer_fields = array(
        'customer_group_id' => 'Group',
        'customer_prefix' => 'Prefix',
        'customer_firstname' => 'First Name',
        'customer_middlename' => 'Middle Name/Initial',
        'customer_lastname' => 'Last Name',
        'customer_suffix' => 'Suffix',
        'customer_email' => 'Email',
    );

    public function CustomerInfoOrderField($order)
    {
        return array(
            'customer_group_id' => array('value' => $order['customer_group_id'], 'title' => 'Group', 'required' => true),
            'customer_prefix' => array('value' => $order['customer_prefix'], 'title' => 'Prefix', 'required' => false),
            'customer_firstname' => array('value' => $order['customer_firstname'], 'title' => 'First Name', 'required' => true),
            'customer_middlename' => array('value' => $order['customer_middlename'], 'title' => 'Middle Name/Initial', 'required' => false),
            'customer_lastname' => array('value' => $order['customer_lastname'], 'title' => 'Last Name', 'required' => true),
            'customer_suffix' => array('value' => $order['customer_suffix'], 'title' => 'Suffix', 'required' => false),
            'customer_email' => array('value' => $order['customer_email'], 'title' => 'Email', 'required' => true),
        );
    }

    public function updateOrderCustomer($params)
    {
        $this->init($params);

        if (isset($params['confirm_edit']) && !empty($params['confirm_edit'])) {
            $this->addChangesToConfirm();
        } else {
            $this->editCustomerInfo();
            $this->updateOrderAmounts();
            $this->addChangesToLog();
            $this->notifyEmail();
        }
    }

    public function execUpdateOrderCustomer($params)
    {
        $this->init($params);
        $this->editCustomerInfo();
        $this->updateOrderAmounts();
        $this->notifyEmail();
        return true;
    }

    protected function init($params)
    {
        $this->params = $params;

        if (empty($this->params) || !isset($this->params['order_id'])) {
            throw new Exception("Order id is not defined");
        }
    }

    protected function updateOrderAmounts()
    {
        if (isset($this->params['recalculate_amount']) && !empty($this->params['recalculate_amount'])) {
            //TODO: add!!!
        }
    }

    protected function notifyEmail()
    {
    }
}
