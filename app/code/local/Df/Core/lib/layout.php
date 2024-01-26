<?php
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
 * @throws Exception
 */
function df_block($block = null, $params = []):Mage_Core_Block_Abstract {/** @var Mage_Core_Block_Abstract $r */
	if (is_string($params)) {
		$params = ['template' => $params];
	}
	if (!$block) {
		$block = 'Mage_Core_Block_Template';
	}
	if (!is_object($block)) {
		$r = df_layout()->getBlockInstance($block, $params);
	}
	else {
		/**
		 * @uses Mage_Core_Model_Layout::createBlock() не добавит параметры к блоку,
		 * если в этот метод передать не тип блока, а еще созданный объект-блок.
		 */
		df_assert($block instanceof Mage_Core_Block_Abstract);
		$block->addData($params);
		$r = $block;
	}
	return $r;
}

/**
 * 2015-03-30
 * Создаёт блок и добавляет его в макет.
 * Используйте эту функцию, если блок нужно нарисовать не сразу
 * (посредством явного и немедленного вызова @see Mage_Core_Block_Abstract::toHtml()),
 * а при отображении макета.
 *
 * Если блок нуждается в методе @see Mage_Core_Block_Abstract::getLayout(),
 * но блок нужно нарисовать сразу, но используйте функцию @see df_render_l():
 * она позволяет блоку использовать макет, но не добавляет блок в макет,
 * а рисует блок сразу.
 *
 * Если блок не нуждается в макете, то используйте функции
 * @see df_block(), @see df_render(), @see df_render_simple()
 *
 * В качестве параметра $block можно передавать:
 * 1) объект-блок
 * 2) класс блока в стандартном формате
 * 3) класс блока в формате Magento
 * 4) пустое значение: в таком случае будет создан блок типа @see Mage_Core_Block_Template
 *
 * @param string|Mage_Core_Block_Abstract|null $block [optional]
 * @param string|array(string => mixed) $params [optional]
 * @return Mage_Core_Block_Abstract
 * @throws Exception
 */
function df_block_l($block, $params = []):Mage_Core_Block_Abstract {/** @var Mage_Core_Block_Abstract $r */
	if (!($r = df_layout()->createBlock(df_block($block, $params)))) {
		df_error("Не могу создать блок класса «{$block}».\nСмотрите отчёт в папке var/log.");
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
 * @used-by df_render_l()
 * @used-by df_render_simple()
 * @param string|Mage_Core_Block_Abstract|null $block [optional]
 * @param string|array(string => mixed) $params [optional]
 * @throws Exception
 */
function df_render($block, $params = []):string {return df_block($block, $params)->toHtml();}

/**
 * 2015-04-01
 * @param Mage_Core_Block_Abstract $parent
 * @param string|Mage_Core_Block_Abstract|null $block [optional]
 * @param string|array(string => mixed) $params [optional]
 * @throws Exception
 */
function df_render_child(Mage_Core_Block_Abstract $parent, $block, $params = []):string {
	return df_block($block, $params)->setParentBlock($parent)->toHtml();
}

/**
 * 2015-04-01
 * Используйте эту функцию для отображения блоков, которые используют метод
 * @see Mage_Core_Block_Abstract::getLayout()
 * Если блок не нужно рисовать сразу, а нужно непременно добавить в макет,
 * то используйте функцию @see df_block_l()
 * @used-by Df_Cms_Block_Admin_Hierarchy_Widget_Chooser::prepareElementHtml()
 * @param string|Mage_Core_Block_Abstract|null $block [optional]
 * @param string|array(string => mixed) $params [optional]
 * @throws Exception
 */
function df_render_l($block, $params = []):string {return df_render(df_block($block, $params)->setLayout(df_layout()));}

/**
 * 2015-03-30
 * Обратите внимание, что эта функция:
 * 1) не добавляет блок в макет, в отличие от @see df_block_l()
 * 2) отображает блок упрощённым методом @uses Mage_Core_Block_Abstract::toHtmlFast()
 * вместо @see Mage_Core_Block_Abstract::toHtml()
 * @used-by df_render_simple_child()
 * @used-by \Df\Qa\Message::message()
 */
function df_render_simple(string $t, array $params = []):string {return df_render(null, ['template' => $t] + $params);}

/**
 * 2015-04-01
 * 2023-01-26 @unused
 * @param string|array(string => mixed) $params [optional]
 */
function df_render_simple_child(Mage_Core_Block_Template $parent, string $templateShort, $params = []):string {
	return df_render_simple(Mage::getDesign()->getTemplateFilename(
		/**
		 * Обратите внимание, что мы намеренно используем @uses Mage_Core_Block_Template::getTemplate()
		 * вместо @see Mage_Core_Block_Template::getTemplateFile(),
		 * чтобы позволить домернему шаблону быть перекрытым (например, в папке priority или base)
		 * независимо от шаблона родителя и наоборот.
		 * @var string $template
		 */
		df_strip_ext($parent->getTemplate()) . "/{$templateShort}.phtml"
		/** по аналогии с @see Mage_Core_Block_Template::getTemplateFile() */
		,df_clean(['_relative' => true, '_area' => $parent->getArea()])
	), $params);
}