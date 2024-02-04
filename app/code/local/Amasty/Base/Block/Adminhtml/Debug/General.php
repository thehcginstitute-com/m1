<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Base
 */ 
class Amasty_Base_Block_Adminhtml_Debug_General extends Amasty_Base_Block_Adminhtml_Debug_Base
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amasty/ambase/debug/general.phtml');        
    }
    
    function getDisableModulesOutput() {
        $config = array();
        
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        
        $tableName = $resource->getTableName('core/config_data');
        
        $query = "SELECT * FROM " . $tableName . " WHERE path LIKE '%advanced/modules_disable_output%' AND value = 1";
 
        $data = $readConnection->fetchAll($query);
        
        foreach($data as $item){
            $config[] = array(
                "name" => str_replace("advanced/modules_disable_output/", "", $item["path"])
            );
        }
        
        return $config;
    }
    
    function isCompilationEnabled() {
		# 2024-02-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Delete the unused `Mage_Compiler` module": https://github.com/thehcginstitute-com/m1/issues/363
		return false;
	}

    
    function getCrontabConfig() {

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        
        $tableName = $resource->getTableName('cron/schedule');
        
        $query = "SELECT * FROM " . $tableName . "  order by schedule_id desc limit 5";
 
        $data = $readConnection->fetchAll($query);
        
        return $data;
    }
    
}