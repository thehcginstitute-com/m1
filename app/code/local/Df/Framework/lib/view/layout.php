<?php
use Mage_Core_Block_Abstract as A;
use Mage_Core_Block_Template as T;
/**
 * В качестве параметра $block можно передавать:
 * 1) объект-блок
 * 2) класс блока в стандартном формате
 * 3) класс блока в формате Magento
 * 4) пустое значение: в таком случае будет создан блок типа @see Mage_Core_Block_Template
 * @used-by df_block_l()
 * @used-by df_render()
 * @param string|Mage_Core_Block_Abstract|null $block [optional]
 * @param string|array(string => mixed) $params [optional]
 * @return A|T
 * @throws Exception
 */
function df_block($c, $data = [], string $template = '', array $vars = []):A {
	if (is_string($data)) {
		$template = $data;
		$data = [];
	}
	if (!$c) {
		$c = T::class;
	}
	if (!is_object($c)) {
		$r = df_layout()->getBlockInstance($c, $data);
	}
	else {
		/**
		 * @uses Mage_Core_Model_Layout::createBlock() не добавит параметры к блоку,
		 * если в этот метод передать не тип блока, а еще созданный объект-блок.
		 */
		df_assert($c instanceof Mage_Core_Block_Abstract);
		$c->addData($data);
		$r = $c;
	}
	# 2019-06-11
	if ($r instanceof T) {
		# 2016-11-22
		$r->assign($vars);
	}
	if ($template && $r instanceof T) {
		$r->setTemplate($template);
	}
	return $r;
}

/**
 * @used-by df_block()
 * @used-by df_block_l()
 * @used-by df_render_l()
 */
function df_layout():Df_Core_Model_Layout {return Mage::getSingleton('core/layout');}

/**
 * 2015-03-30
 * Обратите внимание, что эта функция:
 * 1) не добавляет блок в макет, в отличие от @see df_block_l()
 * 2) отображает блок упрощённым методом @uses Mage_Core_Block_Abstract::toHtmlFast()
 * вместо @see Mage_Core_Block_Abstract::toHtml()
 *
 * В качестве параметра $block можно передавать:
 * 1) объект-блок
 * 2) класс блока в стандартном формате
 * 3) класс блока в формате Magento
 * 4) пустое значение: в таком случае будет создан блок типа @see Mage_Core_Block_Template
 * @used-by df_render_simple()
 * @param string|Mage_Core_Block_Abstract|null $block [optional]
 * @param string|array(string => mixed) $params [optional]
 * @throws Exception
 */
function df_render($block, $params = [], array $vars = []):string {return df_block($block, $params, '',$vars)->toHtml();}

/**
 * 2015-03-30
 * Обратите внимание, что эта функция:
 * 1) не добавляет блок в макет, в отличие от @see df_block_l()
 * 2) отображает блок упрощённым методом @uses Mage_Core_Block_Abstract::toHtmlFast()
 * вместо @see Mage_Core_Block_Abstract::toHtml()
 * @used-by df_render_simple_child()
 * @used-by app/design/adminhtml/default/default/template/sales/order/view/info.phtml
 */
function df_render_simple(string $t, array $params = [], array $vars = []):string {return df_render(
	null, ['template' => $t] + $params, $vars
);}