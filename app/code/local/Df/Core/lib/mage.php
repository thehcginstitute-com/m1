<?php
/**
 * @used-by df_visitor_ip()
 * @return Mage_Core_Helper_Http
 */
function df_mage_http_h() {return Mage::helper('core/http');}

/**
 * @used-by df_current_url()
 * @return Mage_Core_Helper_Url
 */
function df_mage_url_h() {return Mage::helper('core/url');}