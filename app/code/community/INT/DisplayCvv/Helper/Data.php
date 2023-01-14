<?php

class INT_DisplayCvv_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function removeCVV($payement_quote_id)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        
        $connection->update("sales_flat_quote_payment",array("cc_cid_enc" => ''),"quote_id=$payement_quote_id");
        echo $payement_quote_id;
    }

}