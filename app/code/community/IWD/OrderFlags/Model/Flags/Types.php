<?php

/**
 * Class IWD_OrderFlags_Model_Flags_Types
 *
 * @method string getName()
 * @method IWD_OrderFlags_Model_Flags_Types setName(string $value)
 * @method string getComment()
 * @method IWD_OrderFlags_Model_Flags_Types setComment(string $value)
 * @method string getPosition()
 * @method IWD_OrderFlags_Model_Flags_Types setPosition(string $value)
 * @method string getStatus()
 * @method IWD_OrderFlags_Model_Flags_Types setStatus(string $value)
 */
class IWD_OrderFlags_Model_Flags_Types extends Mage_Core_Model_Abstract
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('iwd_orderflags/flags_types');
    }

    /**
     * @return bool
     */
    public function isTypeActive()
    {
        $helper = Mage::helper('iwd_orderflags');
        if ($helper->isIwdOrderGridEnabled()) {
            $columns = $this->getColumns();
            return in_array($this->getOrderGridId(), $columns);
        }

        return $this->getStatus();
    }

    /**
     * @return bool|string
     */
    public function getColumnAfter()
    {
        $helper = Mage::helper('iwd_orderflags');
        if ($helper->isIwdOrderGridEnabled()) {
            $columns = $this->getColumns();
            if (in_array($this->getOrderGridId(), $columns)) {
                $index = array_search($this->getOrderGridId(), $columns);
                return isset($columns[$index - 1]) ? $columns[$index - 1] : 'status';
            }
        }

        return $this->getPosition();
    }

    protected function getColumns()
    {
        $columns = Mage::getStoreConfig('iwd_ordermanager/grid_order/columns');
        return explode(',', $columns);
    }

    /**
     * @return array
     */
    public function toOptionArray()
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
     * @return void
     */
    public function assignFlags()
    {
        $flags = Mage::getModel('iwd_orderflags/flags_flag_type')->getCollection()
            ->addFieldToFilter('type_id', $this->getId())
            ->getColumnValues('flag_id');

        $this->setData('flags', $flags);
    }

    /**
     * @return array
     */
    public function getAssignedFlags()
    {
        $tableFlags = Mage::getSingleton('core/resource')->getTableName('iwd_om_flags');

        $collection = Mage::getModel('iwd_orderflags/flags_flag_type')->getCollection()
            ->addFieldToFilter('type_id', $this->getId());

        $collection->getSelect()->joinLeft(
            $tableFlags,
            "main_table.flag_id = {$tableFlags}.id",
            array("flag_name" => "{$tableFlags}.name")
        );

        $options = array();
        foreach ($collection as $item) {
            $options[$item->getFlagId()] = $item->getFlagName();
        }

        return $options;
    }

    /**
     * @param $flags
     */
    public function assignFlagsToType($flags)
    {
        $flagsTypes = Mage::getModel('iwd_orderflags/flags_flag_type')->getCollection()
            ->addFieldToFilter('type_id', $this->getId());

        foreach ($flagsTypes as $item) {
            if (($key = array_search($item->getFlagId(), $flags)) !== false) {
                unset($flags[$key]);
            } else {
                $item->delete();
            }
        }

        foreach ($flags as $flag) {
            $flagType = Mage::getModel('iwd_orderflags/flags_flag_type');
            $flagType->setTypeId($this->getId())
                ->setFlagId($flag)
                ->save();
        }
    }

    /**
     * @return string
     */
    public function getOrderGridId()
    {
        return 'iwd_om_flags_' . $this->getId();
    }
}
