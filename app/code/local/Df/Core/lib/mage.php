<?php
use Mage_Core_Helper_Http as Http;
use Mage_Core_Helper_Url as Url;

/**
 * @used-by df_visitor_ip()
 */
function df_mage_http_h():Http {return Mage::helper('core/http');}

/**
 * @used-by df_current_url()
 */
function df_mage_url_h():Url {return Mage::helper('core/url');}