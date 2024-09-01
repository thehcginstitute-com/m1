<?php
use Mage_Core_Block_Abstract as B;
class Df_Core_Model_Layout extends Mage_Core_Model_Layout {
	/**
	 * Публичный доступ к системному методу @uses _getBlockInstance()
	 * @used-by df_block()
	 * @param array(string => mixed) $d
	 */
	function getBlockInstance(B $b, array $d = []):B {return $this->_getBlockInstance($b, $d);}

	/**
	 * Оповещает разработчика о сбоях при создании блоков.
	 * @see Mage_Core_Model_Layout::createBlock:
	 *	 try {
	 *		 $block = $this->_getBlockInstance($type, $attributes);
	 *	 } catch (Exception $e) {
	 *		 Mage::logException($e);
	 *		 return false;
	 *	 }
	 * При стандартном поведении Magento просто записывает сообщение о сбое в журнал сбоев.
	 * Там это сообщение остаётся, как правило, незамеченным администратором и разработчиком!
	 * @override
	 * @param B|string $b
	 * @param array(string => mixed) $attributes
	 * @throws Exception
	 */
	protected function _getBlockInstance($b, array $attributes=[]):B {/** @var B $r */
		try {
			$r = parent::_getBlockInstance($b, $attributes);
		}
		catch (Exception $e) {
			df_log($e);
			df_error(is_string($b) ? "Не могу создать блок класса «{$b}»" : (
				is_object($b) ? sprintf("Класс «%s» недопустим для блока", get_class($b)) : $e
			));
		}
		return $r;
	}
}