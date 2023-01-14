<?php
/**
 * @category   Zoho
 * @package    Zoho_Salesiq
 * @author     SalesIQ Team
 * @website    http://www.zoho.com/salesiq
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Zoho_Salesiq_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getConfig($field, $section = 'option', $default = null) {
        $value = Mage::getStoreConfig('salesiq/' . $section . '/' . $field);
        if (!isset($value) or trim($value) == '') {
            return $default;
        } else {
            return $value;
        }
    }

    public function log($data) {
		    if (!$this->getConfig('enable_log')) {
			      return;
		    }

        if (is_array($data) || is_object($data)) {
            $data = print_r($data, true);
        }

        Mage::log($data, null, 'salesiq.log');
    }

  	public function getWidgetcode() {
    		return $this->getConfig('widget_code');
	  }

  	public function isChatWindowEnabled() {
    		return $this->getConfig('chat_window');
	  }

    public function getIncludedPages() {
        return explode(',', $this->getConfig('include_in', 'option', 'all_pages'));
    }

    public function isQuickChatEnabled() {
        return $this->getConfig('quick_chat_button');
    }

}
