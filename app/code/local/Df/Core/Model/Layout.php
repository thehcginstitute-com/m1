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
	 * @param array(string => mixed) $attributes
	 * @throws Exception
	 */
	protected function _getBlockInstance($block, array $attributes=[]):B {/** @var B $r */
		try {
			$r = parent::_getBlockInstance($block, $attributes);
		}
		catch (Exception $e) {
			df_log($e);
			/** @var string $commonPart */
			$commonPart = "\nСмотрите отчёт в папке var/log.";
			if (is_string($block)) {
				df_error("Не могу создать блок класса «{$block}».{$commonPart}");
			}
			elseif (is_object($block)) {
				df_error("Класс «%s» недопустим для блока.{$commonPart}", get_class($block));
			}
			else {
				throw $e;
			}
		}
		return $r;
	}
}