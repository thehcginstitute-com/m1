<?php

class Pma_Importer_Model_Mysql4_Importer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

     function _construct() {
        parent::_construct();
        $this->_init('importer/importer');
    }

}
