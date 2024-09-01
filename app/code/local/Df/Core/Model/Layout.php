<?php
use Mage_Core_Block_Abstract as B;
use Throwable as T;
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
	 * @see Mage_Core_Model_Layout::_getBlockInstance()
	 * @param B|string $b
	 * @param array(string => mixed) $attributes
	 * @throws T
	 */
	protected function _getBlockInstance($b, array $d = []):B {return df_try(
		function() use($b, $d) {return parent::_getBlockInstance($b, $d);}
		,function(T $t) {df_log($t); throw $t;}
	);}
}