<?php

/**
 * Class IWD_SettlementReport_Block_Adminhtml_Transactions_Renderer_Colorize_Amount
 */
class IWD_SettlementReport_Block_Adminhtml_Transactions_Renderer_Colorize_Amount
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency
{
    /**
     * @var mixed|null
     */
    protected $forCompareColl = null;

    /**
     * @var bool|mixed
     */
    protected $export = false;

    /**
     * IWD_SettlementReport_Block_Adminhtml_Transactions_Renderer_Colorize_Amount constructor.
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        if (!empty($args['forCompareColl'])) {
            $this->forCompareColl = $args['forCompareColl'];
        }

        if (isset($args['export'])) {
            $this->export = $args['export'];
        }

        parent::__construct($args);
    }

    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        if ($this->export) {
            return parent::render($row);
        }

        $currentAmount = $row->getData($this->getColumn()->getIndex());
        $compareAmount = $row->getData($this->forCompareColl);

        $amount = (string)parent::render($row);
        return ($currentAmount == $compareAmount) ? $amount : "<b style='color:#C54E35;'>{$amount}</b>";
    }
}
