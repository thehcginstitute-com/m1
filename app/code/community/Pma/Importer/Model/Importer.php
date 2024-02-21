<?php

class Pma_Importer_Model_Importer extends Mage_Core_Model_Abstract {

     function _construct() {
        parent::_construct();
        $this->_init('importer/importer');
    }

}
