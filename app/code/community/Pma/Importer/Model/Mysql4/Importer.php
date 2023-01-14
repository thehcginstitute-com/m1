<?php

class Pma_Importer_Model_Mysql4_Importer extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('importer/importer', 'id');
    }

}
