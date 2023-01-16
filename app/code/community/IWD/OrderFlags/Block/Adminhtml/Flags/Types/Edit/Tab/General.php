<?php

/**
 * Class IWD_OrderFlags_Block_Adminhtml_Flags_Types_Edit_Tab_General
 */
class IWD_OrderFlags_Block_Adminhtml_Flags_Types_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected $form;

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        try {
            $this->form = new Varien_Data_Form();
            $this->setForm($this->form);
            $this->addFieldsetToFormGeneral();
            $this->setValuesToForm();
        } catch (Exception $e) {
            IWD_OrderFlags_Model_Logger::log($e . __CLASS__);
        }

        return parent::_prepareForm();
    }

    protected function addFieldsetToFormGeneral()
    {
        $helper = Mage::helper('iwd_orderflags');

        $fieldset = $this->form->addFieldset(
            'iwd_om_flags_types_general_info',
            array('legend' => $helper->__('General Information'))
        );

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => $helper->__('Name'),
                'title' => $helper->__('Name'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'name',
            )
        );

        $fieldset->addField(
            'comment',
            'text',
            array(
                'label' => $helper->__('Comment'),
                'title' => $helper->__('Comment'),
                'required' => false,
                'name' => 'comment',
            )
        );

        if ($this->isEdit()) {
            if ($helper->isIwdOrderGridEnabled()) {
                $fieldset->addField(
                    'iwd_order_grid',
                    'label',
                    array(
                        'label' => $helper->__('Position'),
                        'title' => $helper->__('Position'),
                        'after_element_html' => '<p style="max-width:280px">Manage column position under \'Order Table Columns\' in <a href="'
                            . $this->getUrl('adminhtml/system_config/edit', array('section' => 'iwd_ordergrid'))
                            . '" target="_blank">extension settings</a>.</p>'
                    )
                );
            } else {
                $fieldset->addField(
                    'position',
                    'select',
                    array(
                        'label' => $helper->__('Position After'),
                        'title' => $helper->__('Position After'),
                        'values' => $this->getGridColumns(),
                        'name' => 'position',
                    )
                );

                $fieldset->addField(
                    'status',
                    'select',
                    array(
                        'label' => $helper->__('Display Column In Order Table'),
                        'title' => $helper->__('Display Column In Order Table'),
                        'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
                        'name' => 'status',
                    )
                );
            }
        }
    }

    /**
     * @return array
     */
    protected function getGridColumns()
    {
        $helper = Mage::helper('iwd_orderflags');

        $columns = array(
            'increment_id' => $helper->__('Order #'),
            'status' => $helper->__('Status'),
            'store_id' => $helper->__('Purchased From (Store)'),
            'grand_total' => $helper->__('G.T. (Purchased)'),
            'base_grand_total' => $helper->__('G.T. (Base)'),
            'created_at' => $helper->__('Created At (Purchased On)'),
            'updated_at' => $helper->__('Updated At'),
            'total_paid' => $helper->__('Total Paid'),
            'shipping_name' => $helper->__('Ship - Name'),
            'billing_name' => $helper->__('Bill - Name'),
        );

        return $columns;
    }

    protected function setValuesToForm()
    {
        $types = $this->getFlagTypesData();
        $data = ($types && !is_array($types)) ? $types->getData() : array();
        $this->form->setValues($data);
    }

    /**
     * @return mixed
     */
    protected function getFlagTypesData()
    {
        return Mage::registry('iwd_om_flags_types');
    }

    /**
     * @return bool
     */
    protected function isEdit()
    {
        $data = $this->getFlagTypesData();
        return (!empty($data) && $data->getId());
    }
}
