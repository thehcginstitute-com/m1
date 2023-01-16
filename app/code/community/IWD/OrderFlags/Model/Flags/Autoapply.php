<?php

/**
 * Class IWD_OrderFlags_Model_Flags_Autoapply
 *
 * @method string getFlagId()
 * @method IWD_OrderFlags_Model_Flags_Autoapply setFlagId(string $value)
 * @method string getTypeId()
 * @method IWD_OrderFlags_Model_Flags_Autoapply setTypeId(string $value)
 * @method string getApplyType()
 * @method IWD_OrderFlags_Model_Flags_Autoapply setApplyType(string $value)
 * @method string getKey()
 * @method IWD_OrderFlags_Model_Flags_Autoapply setKey(string $value)
 */
class IWD_OrderFlags_Model_Flags_Autoapply extends Mage_Core_Model_Abstract
{
    const TYPE_ORDER_STATUS = 'status';
    const TYPE_SHIPPING_METHOD = 'shipping';
    const TYPE_PAYMENT_METHOD = 'payment';
    const TYPE_STORE_VIEW = 'store_view';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('iwd_orderflags/flags_autoapply');
    }
}
