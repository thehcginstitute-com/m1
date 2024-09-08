<?php

/**
 * Class IWD_OrderFlags_Adminhtml_Flags_TypesController
 */
class IWD_OrderFlags_Adminhtml_Flags_TypesController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return void
     */
    function indexAction()
    {
        $this->_initAction();

        $this->_addContent($this->getLayout()->createBlock('iwd_orderflags/adminhtml_flags_types'));
        $this->renderLayout();
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system')
            ->_title($this->__('IWD - Columns for Flags'));

        $this->_addBreadcrumb(
            Mage::helper('iwd_orderflags')->__('Columns for Flags'),
            Mage::helper('iwd_orderflags')->__('Columns for Flags')
        );

        return $this;
    }

    /**
     * @return void
     */
    function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_orderflags/adminhtml_flags_types_grid')->toHtml()
        );
    }

    /**
     * @return void
     */
    function newAction()
    {
        $this->_initAction();
        $this->prepareDefaultFormData();
        $this->typesForm();
    }

    /**
     * @return void
     */
    function editAction()
    {
        $this->_initAction();
        $this->prepareFormData();
        $this->typesForm();
    }

    /**
     * @return void
     */
    protected function typesForm()
    {
        $this->_addContent($this->getLayout()->createBlock('iwd_orderflags/adminhtml_flags_types_edit'))
            ->_addLeft($this->getLayout()->createBlock('iwd_orderflags/adminhtml_flags_types_edit_tabs'));

        $this->renderLayout();
    }

    /**
     * @return void
     */
    protected function prepareFormData()
    {
        $id = $this->getRequest()->getParam('id');
        $type = Mage::getModel('iwd_orderflags/flags_types')->load($id);
        $type->assignFlags();

        if ($type->getId()) {
            Mage::register('iwd_om_flags_types', $type);
        }
    }

    /**
     * @return void
     */
    protected function prepareDefaultFormData()
    {
        $flagType = Mage::getModel('iwd_orderflags/flags_types');
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $flagType->setData($data);
        }

        Mage::register('iwd_om_flags_types', $flagType);
    }

    /**
     * @return void
     */
    function saveAction()
    {
        $helper = Mage::helper('iwd_orderflags');
        try {
            $type = $this->saveType();
            $this->assignFlagsToType($type);

            Mage::getSingleton('adminhtml/session')->setFormData(false);
            Mage::getSingleton('adminhtml/session')->addSuccess($helper->__('Type was successfully saved'));

            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', array('id' => $type->getId()));
                return;
            }
        } catch (Exception $e) {
            IWD_OrderFlags_Model_Logger::log(
                $e->getMessage(),
                $helper->__('Type was not saved.') . ' ' . $e->getMessage()
            );
        }

        $this->_redirect('*/*/');
    }

    /**
     * @return void
     */
    function deleteAction()
    {
        $helper = Mage::helper('iwd_orderflags');

        try {
            $type = $this->getTypeObj();
            if ($type->getId() == 1) {
                Mage::getSingleton('adminhtml/session')->addNotice(
                    $helper->__('You can not remove default type of flags.')
                );
            } else {
                $type->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess($helper->__('Type was successfully deleted'));
            }
        } catch (Exception $e) {
            IWD_OrderFlags_Model_Logger::log(
                $e->getMessage(),
                Mage::helper('iwd_orderflags')->__('Type was not deleted.') . ' ' . $e->getMessage()
            );
        }

        $this->_redirect('*/*/');
    }

    /**
     * @return void
     */
    function massDeleteAction()
    {
        $helper = Mage::helper('iwd_orderflags');

        try {
            $deleted = 0;
            $ids = $this->getRequest()->getParam('id', array());
            foreach ($ids as $id) {
                if ($id != 1) {
                    $flag = Mage::getModel('iwd_orderflags/flags_types')->load($id);
                    $flag->delete();
                    $deleted++;
                } else {
                    Mage::getSingleton('adminhtml/session')->addNotice(
                        Mage::helper('iwd_orderflags')->__('You can not remove default type of flags.')
                    );
                }
            }

            if ($deleted) {
                Mage::getSingleton('adminhtml/session')->addSuccess($helper->__('Type(s) was successfully deleted'));
            }
        } catch (Exception $e) {
            IWD_OrderFlags_Model_Logger::log(
                $e->getMessage(),
                $helper->__('Type(s) was not deleted.') . ' ' . $e->getMessage()
            );
        }

        $this->_redirect('*/*/');
    }

    /**
     * @return IWD_OrderFlags_Model_Flags_Types
     */
    protected function saveType()
    {
        $name = $this->getRequest()->getParam('name', null);
        $comment = $this->getRequest()->getParam('comment', '');
        $position = $this->getRequest()->getParam('position', 'status');
        $status = $this->getRequest()->getParam('status', '1');

        $flagType = $this->getTypeObj();
        $flagType->setName($name)
            ->setComment($comment)
            ->setPosition($position)
            ->setStatus($status)
            ->save();

        return $flagType;
    }

    /**
     * @param $type IWD_OrderFlags_Model_Flags_Types
     */
    protected function assignFlagsToType($type)
    {
        $flags = $this->getRequest()->getParam('flags', array());
        $type->assignFlagsToType($flags);
    }

    /**
     * @return IWD_OrderFlags_Model_Flags_Types
     */
    protected function getTypeObj()
    {
        $id = $this->getRequest()->getParam('id', null);
        $flagType = Mage::getModel('iwd_orderflags/flags_types');
        if ($id != null) {
            $flagType->load($id);
        }

        return $flagType;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        //TODO:!!!
        return Mage::getSingleton('admin/session')->isAllowed('system/iwdall/iwd_orderflags/iwd_flags');
    }
}
