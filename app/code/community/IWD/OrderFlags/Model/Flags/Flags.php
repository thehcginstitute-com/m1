<?php

/**
 * Class IWD_OrderFlags_Model_Flags_Flags
 *
 * @method string getName()
 * @method IWD_OrderFlags_Model_Flags_Flags setName(string $value)
 * @method string getIconType()
 * @method IWD_OrderFlags_Model_Flags_Flags setIconType(string $value)
 * @method IWD_OrderFlags_Model_Flags_Flags setIconImage(string $value)
 * @method string getIconFa()
 * @method IWD_OrderFlags_Model_Flags_Flags setIconFa(string $value)
 * @method string getIconFaColor()
 * @method IWD_OrderFlags_Model_Flags_Flags setIconFaColor(string $value)
 * @method string getComment()
 * @method IWD_OrderFlags_Model_Flags_Flags setComment(string $value)
 */

class IWD_OrderFlags_Model_Flags_Flags extends Mage_Core_Model_Abstract
{
    protected $allowImageFormats = array('jpg', 'jpeg', 'png', 'gif');

    const FILES_DISPERSION = true;
    const BASE_IWD_OM_MEDIA_DIR = 'iwd_ordermanager';
    const IMAGE_DIR = 'flags';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('iwd_orderflags/flags_flags');
    }

    /**
     * @return array
     */
    function toOptionArray()
    {
        $options = array(
            array(
                'label' => Mage::helper('adminhtml')->__('--Please Select--'),
                'value' => '0'
            )
        );

        $collection = $this->getCollection();
        foreach ($collection as $item) {
            $options[] = array(
                'label' => $item->getName(),
                'value' => $item->getId()
            );
        }

        return $options;
    }

    /**
     * @return $this
     */
    function saveFlagImage()
    {
        if (isset($_FILES['icon_img']['name']) && !empty($_FILES['icon_img']['name'])) {
            $result = $this->uploadImage('icon_img', $_FILES['icon_img']['name']);
            if ($result !== false) {
                $this->setData("icon_img", $result['file'])->save();
            }
        }

        return $this;
    }

    /**
     * @return void
     */
    function assignTypes()
    {
        $types = self::getAssignTypesForFlag($this->getId());
        $this->setData('types', $types);
    }

    public static function getAssignTypesForFlag($flagId)
    {
        return Mage::getModel('iwd_orderflags/flags_flag_type')->getCollection()
            ->addFieldToFilter('flag_id', $flagId)
            ->getColumnValues('type_id');
    }

    /**
     * @return void
     */
    function assignDisallowedAutoApplyOptions()
    {
        $status = $this->getAutoApplyOptions(IWD_OrderFlags_Model_Flags_Autoapply::TYPE_ORDER_STATUS, 'neq');
        $payment = $this->getAutoApplyOptions(IWD_OrderFlags_Model_Flags_Autoapply::TYPE_PAYMENT_METHOD, 'neq');
        $shipping = $this->getAutoApplyOptions(IWD_OrderFlags_Model_Flags_Autoapply::TYPE_SHIPPING_METHOD, 'neq');
        $storeView = $this->getAutoApplyOptions(IWD_OrderFlags_Model_Flags_Autoapply::TYPE_STORE_VIEW, 'neq');

        $disallowedAutoApplyOptions = array(
            'order_status' => $status,
            'payment_method' => $payment,
            'shipping_method' => $shipping,
            'store_view' => $storeView
        );

        $this->setData('disallowed_autoapply_options', $disallowedAutoApplyOptions);
    }

    /**
     * @return void
     */
    function assignAutoApplyOptions()
    {
        $status = $this->getAutoApplyOptions(IWD_OrderFlags_Model_Flags_Autoapply::TYPE_ORDER_STATUS);
        $payment = $this->getAutoApplyOptions(IWD_OrderFlags_Model_Flags_Autoapply::TYPE_PAYMENT_METHOD);
        $shipping = $this->getAutoApplyOptions(IWD_OrderFlags_Model_Flags_Autoapply::TYPE_SHIPPING_METHOD);
        $storeView = $this->getAutoApplyOptions(IWD_OrderFlags_Model_Flags_Autoapply::TYPE_STORE_VIEW);

        $this->setData('order_status', $status);
        $this->setData('payment_method', $payment);
        $this->setData('shipping_method', $shipping);
        $this->setData('store_view', $storeView);
    }

    /**
     * @param $type
     * @param string $equal
     * @return mixed
     */
    function getAutoApplyOptions($type, $equal = 'eq')
    {
        return Mage::getModel('iwd_orderflags/flags_autoapply')->getCollection()
            ->addFieldToFilter('flag_id', array($equal => $this->getId()))
            ->addFieldToFilter('apply_type', $type)
            ->getColumnValues('method_key');
    }

    /**
     * @param $fieldId
     * @param null $fileName
     * @return bool|void
     * @throws Exception
     */
    protected function uploadImage($fieldId, $fileName = null)
    {
        $path = $this->getMediaImageDir();
        $uploader = new Varien_File_Uploader($fieldId);
        $uploader->setAllowedExtensions($this->allowImageFormats);
        $uploader->setAllowCreateFolders(true);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(self::FILES_DISPERSION);

        return $uploader->save($path, $fileName);
    }

    /**
     * .../media/iwd_blog/img/
     *
     * @return string
     */
    protected function getMediaImageDir()
    {
        $path = Mage::getBaseDir('media') . DS . self::BASE_IWD_OM_MEDIA_DIR . DS;
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }

        $path .= self::IMAGE_DIR . DS;
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }

        return $path;
    }

    /**
     * @return string
     */
    function getIconImage()
    {
        if ($this->getData('icon_img')) {
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)
            . self::BASE_IWD_OM_MEDIA_DIR . '/'
            . self::IMAGE_DIR
            . $this->getData('icon_img');
        }

        return '';
    }

    /**
     * @return string
     */
    function getIconHtml()
    {
        $html = '';

        if (!$this->getId()) {
            $html = '<div class="iwd-om-flag-font empty-flag" title="Assign Label" data-flag-id="0"><i class="fa fa-flag"></i></div><span class="hint">Assign Label</span>';
        } elseif ($this->getIconType() == 'image') {
            if ($this->getIconImage()) {
                $html = sprintf('<img class="iwd-om-flag-image" src="%s" title="%s" data-flag-id="%s"/>', $this->getIconImage(), $this->getName(), $this->getId());
            }
        } elseif ($this->getIconType() == 'font') {
            if ($this->getIconFa() && $this->getIconFaColor()) {
                $html = sprintf('<div class="iwd-om-flag-font" title="%s" data-flag-id="%s"><i class="fa %s" style="color:%s"></i></div>', $this->getName(), $this->getId(), $this->getIconFa(), $this->getIconFaColor());
            }
        }

        return $html;
    }

    /**
     * @return string
     */
    function getIconHtmlWithHint()
    {
        $hint = '';
        if ($this->getId()) {
            $hint = '<span class="hint">' . $this->getName() . '. ' . $this->getComment() . '</span>';
        }

        return $this->getIconHtml() . $hint;
    }

    /**
     * @param $orderStatus
     */
    function saveAutoApplyOrderStatuses($orderStatus)
    {
        $orderStatus = $this->deleteAutoApplyOptions(IWD_OrderFlags_Model_Flags_Autoapply::TYPE_ORDER_STATUS, $orderStatus);
        $this->saveAutoApplyOptions(IWD_OrderFlags_Model_Flags_Autoapply::TYPE_ORDER_STATUS, $orderStatus);
    }

    /**
     * @param $paymentMethod
     */
    function saveAutoApplyPaymentMethods($paymentMethod)
    {
        $paymentMethod = $this->deleteAutoApplyOptions(IWD_OrderFlags_Model_Flags_Autoapply::TYPE_PAYMENT_METHOD, $paymentMethod);
        $this->saveAutoApplyOptions(IWD_OrderFlags_Model_Flags_Autoapply::TYPE_PAYMENT_METHOD, $paymentMethod);
    }

    /**
     * @param $shippingMethod
     */
    function saveAutoApplyShippingMethods($shippingMethod)
    {
        $shippingMethod = $this->deleteAutoApplyOptions(IWD_OrderFlags_Model_Flags_Autoapply::TYPE_SHIPPING_METHOD, $shippingMethod);
        $this->saveAutoApplyOptions(IWD_OrderFlags_Model_Flags_Autoapply::TYPE_SHIPPING_METHOD, $shippingMethod);
    }

    /**
     * @param $storeViews
     */
    function saveAutoApplyStoreViews($storeViews)
    {
        $storeViews = $this->deleteAutoApplyOptions(IWD_OrderFlags_Model_Flags_Autoapply::TYPE_STORE_VIEW, $storeViews);
        $this->saveAutoApplyOptions(IWD_OrderFlags_Model_Flags_Autoapply::TYPE_STORE_VIEW, $storeViews);
    }

    /**
     * @param $type
     * @param $options
     * @return mixed
     */
    protected function deleteAutoApplyOptions($type, $options)
    {
        $flagsAutoApply = Mage::getModel('iwd_orderflags/flags_autoapply')->getCollection()
            ->addFieldToFilter('flag_id', $this->getId())
            ->addFieldToFilter('apply_type', $type);

        foreach ($flagsAutoApply as $item) {
            if (($key = array_search($item->getMethodKey(), $options)) !== false) {
                unset($options[$key]);
            } else {
                $item->delete();
            }
        }

        return $options;
    }

    /**
     * @param $type
     * @param $options
     */
    function saveAutoApplyOptions($type, $options)
    {
        foreach ($options as $option) {
            if (empty($option)) {
                continue;
            }

            $flagsAutoApply = Mage::getModel('iwd_orderflags/flags_autoapply');
            $flagsAutoApply->setFlagId($this->getId())
                ->setApplyType($type)
                ->setMethodKey($option)
                ->save();
        }
    }

    /**
     * @param $types
     */
    function saveFlagColumnsRelation($types)
    {
        $types = is_string($types) ? array($types) : $types;

        $flagsTypes = Mage::getModel('iwd_orderflags/flags_flag_type')->getCollection()
            ->addFieldToFilter('flag_id', $this->getId());

        foreach ($flagsTypes as $item) {
            if (($key = array_search($item->getTypeId(), $types)) !== false) {
                unset($types[$key]);
            } else {
                $this->deleteFromOrders($item->getFlagId(), $item->getTypeId());
                $item->delete();
            }
        }

        foreach ($types as $type) {
            $flagType = Mage::getModel('iwd_orderflags/flags_flag_type');
            $flagType->setFlagId($this->getId())
                ->setTypeId($type)
                ->save();
        }
    }

    /**
     * @param $flagId
     * @param $typeId
     */
    protected function deleteFromOrders($flagId, $typeId)
    {
        $collection = Mage::getModel('iwd_orderflags/flags_orders')->getCollection()
            ->addFieldToFilter('flag_id', $flagId)
            ->addFieldToFilter('type_id', $typeId);

        foreach ($collection as $item) {
            $item->delete();
        }
    }

    function delete()
    {
        $flagsTypes = Mage::getModel('iwd_orderflags/flags_flag_type')->getCollection()
            ->addFieldToFilter('flag_id', $this->getId());
        foreach ($flagsTypes as $item) {
            $item->delete();
        }

        $flagsAutoapply = Mage::getModel('iwd_orderflags/flags_autoapply')->getCollection()
            ->addFieldToFilter('flag_id', $this->getId());
        foreach ($flagsAutoapply as $item) {
            $item->delete();
        }

        parent::delete();
    }
}
