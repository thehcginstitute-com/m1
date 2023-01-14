<?php

class OX_AmexSavedCC_Model_Method_Ccsave extends Mage_Payment_Model_Method_Cc
{
    protected $_code        = 'amexsavedcc';
    protected $_canSaveCc   = true;
    protected $_formBlockType = 'payment/form_ccsave';
    protected $_infoBlockType = 'payment/info_ccsave';
}