<?php
/**
 * 2016-11-22
 * $m could be:
 * 		1) A module name: «A_B»
 * 		2) A class name: «A\B\C».
 * 		3) An object: it comes down to the case 2 via @see get_class()
 * 		4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * Параметры $vars будут доступны в шаблоне в качестве переменных:
 * @see \Magento\Framework\View\TemplateEngine\Php::render()
 *		extract($dictionary, EXTR_SKIP);
 * https://github.com/magento/magento2/blob/2.1.2/lib/internal/Magento/Framework/View/TemplateEngine/Php.php#L58
 * 2024-05-23 "Improve `df_block_output()`": https://github.com/mage2pro/core/issues/387
 * @see df_cms_block()
 * @used-by app/design/adminhtml/default/default/template/sales/order/view/info.phtml
 * @param string|object $ct
 * @param array $vars [optional]
 * @param array(string => mixed) $data [optional]
 */
function df_block_output($ct, array $vars = [], array $d = []):string {return df_block(...(
	is_object($ct) || df_class_exists($ct) ? [$ct, $d, '', $vars] : [null, $d, $ct, $vars]
))->toHtml();}