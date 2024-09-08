<?php
/**
 * @category   Zoho
 * @package    Zoho_Salesiq
 * @author     SalesIQ Team
 * @website    http://www.zoho.com/salesiq
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Zoho_Salesiq_Block_Button extends Mage_Core_Block_Template {
  
  function getQuestion() {
    $question = 'Hi, I need to know more about ';
    $question .= $this->helper('catalog/data')->getProduct()->getName();
    $question .= ' (';
    $question .= $this->helper('core/url')->getCurrentUrl();
    $question .= ')';
    return htmlspecialchars(json_encode($question));
  }

  function getLabel() {
    return $this->__('Click here to chat');
  }

  function isQuickChatEnabled() {
    return $this->helper('salesiq')->isQuickChatEnabled();
  }
}
