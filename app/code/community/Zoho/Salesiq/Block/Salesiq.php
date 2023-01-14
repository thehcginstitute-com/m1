<?php
/**
 * @category   Zoho
 * @package    Zoho_Salesiq
 * @author     SalesIQ Team
 * @website    http://www.zoho.com/salesiq
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Zoho_Salesiq_Block_Salesiq extends Mage_Core_Block_Template {

	protected function _toHtml() {
		$helper = Mage::helper('salesiq');
    $includedPages = $helper->getIncludedPages();
    $currentPage = Mage::app()->getFrontController()->getAction()->getFullActionName();

    // If chat window option is enabled, include the widget code only if
    // "All Pages" or the current page is configured in settings.
    if ($helper->isChatWindowEnabled()
        && (in_array('all_pages', $includedPages)
            || in_array($currentPage, $includedPages))) {
      return $this->_getWidgetCode($helper);
    }

    // If chat window is not enabled, but the Quick chat option is enabled
    // and the current page is Product page, then include the widget code
    // but hide the float button and window so that they are shown only if
    // the Quick Chat button is clicked.
    if ($currentPage == 'catalog_product_view'
        && $helper->isQuickChatEnabled()) {
      $extra = '$zs.floatbutton.visible("hide");
                $zs.floatwindow.visible("hide");';
      return $this->_getWidgetCode($helper, $extra);
    }
	}

  protected function _getName() {
    return trim($this->helper('customer/data')->getCustomer()->getName());
  }

  protected function _getEmail() {
    return trim($this->helper('customer/data')->getCustomer()->getEmail());
  }

  protected function _getWidgetCode($helper, $extra="") {
    $code = $helper->getWidgetCode(); 
    $code .= '<script>';
    $code .= 'var $zs = $zoho.salesiq || $zoho.livedesk;';
    $code .= '$zs.ready=function() {';
    $code .= '  $zs.visitor.name("' . $this->_getName() . '");';
    $code .= '  $zs.visitor.email("' . $this->_getEmail() . '");';
    $code .= $extra;
    $code .= '}</script>';
    return $code;
  }

}
