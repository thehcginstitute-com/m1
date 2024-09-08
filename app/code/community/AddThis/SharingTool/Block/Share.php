<?php
/*
 * Copyright (C) 2012 Clearspring Technologies, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
?>
<?php

class AddThis_SharingTool_Block_Share extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('addthis/sharingtool/share.phtml');
    }
    
    function getPluginEnabledStatus(){
    	
    	return Mage::getStoreConfig('sharing_tool/general/enabled');
    }
    
    function getPubId(){
    	 
    	return Mage::getStoreConfig('plugins_general/general/pubid');
    }
    
    function getMenuVersion(){
    	 
    	return Mage::getStoreConfig('sharing_tool/general/menuvx');
    }

    function getButtonStyle(){
    
    	return Mage::getStoreConfig('sharing_tool/button_style/button_set');
    }
    
    function getCustomButtonUrl(){
    
    	return Mage::getStoreConfig('sharing_tool/button_style/custom_button_url');
    }
    
    function getCustomButtonCode(){
    
    	return Mage::getStoreConfig('sharing_tool/button_style/custom_button_code');
    }   
    
    function getExcludeServices(){
    
    	return Mage::getStoreConfig('sharing_tool/api/services_exclude');
    }
    
    function getCompactServices(){
    
    	return Mage::getStoreConfig('sharing_tool/api/services_compact');
    }
    
    function getExpandedServices(){
    	 
    	return Mage::getStoreConfig('sharing_tool/api/services_expanded');
    }
    
    function getCustomServiceName(){
    
    	return Mage::getStoreConfig('sharing_tool/custom_service/services_custom_name');
    }
    
    function getCustomServiceUrl(){
    
    	return Mage::getStoreConfig('sharing_tool/custom_service/services_custom_url');
    }
    
    function getCustomServiceIcon(){
    
    	return Mage::getStoreConfig('sharing_tool/custom_service/services_custom_icon');
    }
        
    function getUiClick(){
    
    	return Mage::getStoreConfig('sharing_tool/api/ui_click');
    }
    
    function getUiDelay(){
    
    	return Mage::getStoreConfig('sharing_tool/api/ui_delay');
    }
    
    function getUiHover(){
    
    	return Mage::getStoreConfig('sharing_tool/api/ui_hover_direction');
    }
    
    function getUiOpenWindows(){
    
    	return Mage::getStoreConfig('sharing_tool/api/ui_open_windows');
    }
    
    function getUiLanguage(){
    
    	return Mage::getStoreConfig('sharing_tool/api/ui_language');
    }
    
    function getDataTrackClick(){
    
    	return Mage::getStoreConfig('sharing_tool/api/data_track_clickback');
    }

    function getAddressBarShare(){
    
    	return Mage::getStoreConfig('sharing_tool/api/address_bar_share');
    }
    
    function getDataGaTracker(){
    
    	return Mage::getStoreConfig('sharing_tool/api/data_ga_tracker');
    }
        
    function getCustomUrl(){
    
    	return Mage::getStoreConfig('sharing_tool/custom_share/custom_url');
    }
    
    function getCustomTitle(){
    
    	return Mage::getStoreConfig('sharing_tool/custom_share/custom_title');
    }
    
    function getCustomDescription(){
    
    	return Mage::getStoreConfig('sharing_tool/custom_share/custom_description');
    }
    
}
