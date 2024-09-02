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