<?php
use Mage_Core_Block_Abstract as A;
use Mage_Core_Block_Template as T;
/**
 * @param string|A|null $c
 * 2015-12-14
 * $c может быть как объектом, так и строкой: https://3v4l.org/udMMH
 * @param string|array(string => mixed) $data [optional]
 * 2016-11-22
 * @param array(string => mixed) $vars [optional]
 * Параметры $vars будут доступны в шаблоне в качестве переменных:
 * @see \Magento\Framework\View\TemplateEngine\Php::render()
 *		extract($dictionary, EXTR_SKIP);
 * https://github.com/magento/magento2/blob/2.1.2/lib/internal/Magento/Framework/View/TemplateEngine/Php.php#L58
 * @return A|T
 * @throws Exception
 */
function df_block($c, $data = [], string $t = '', array $vars = []):A {
	if (is_string($data)) {
		$t = $data;
		$data = [];
	}
	if (!$c) {
		$c = T::class;
	}
	$r = df_layout()->b($c ?: T::class)->addData($data);
	if ($r instanceof T) { # 2019-06-11
		$r->assign($vars); # 2016-11-22
	}
	if ($t && $r instanceof T) {
		$r->setTemplate(df_phtml_add_ext($t));
	}
	return $r;
}

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
 * @param string|object|null $c
 * @param array $vars [optional]
 * @param array(string => mixed) $data [optional]
 */
function df_block_output($c, string $t = '', array $vars = [], array $d = []):string {return df_block(...(
	df_es($t) ? [$c, $d, '', $vars] : [null, $d, $t, $vars]
))->toHtml();}