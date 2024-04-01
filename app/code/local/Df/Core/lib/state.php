<?php
/**
 * Раньше я использовал Mage::app()->getStore()->isAdmin(),
 * однако метод ядра @see Mage_Core_Model_Store::isAdmin()
 * проверяет, является ли магазин административным,
 * более наивным способом: сравнивая идентификатор магазина с нулем
 * (подразумевая, что 0 — идентификатор административного магазина).
 * Как оказалось, у некоторых клиентов идентификатор административного магазина
 * не равен нулю (видимо, что-то не то делали с базой данных).
 * Поэтому используем более надёжную проверку — кода магазина.
 * @param Mage_Core_Model_Store|int|string|bool|null $store
 * @return bool
 *
 * 2015-02-04
 * Раньше реализация метода была такой:
		function df_is_admin($store = null) {
			static $cachedResult;
			$forCurrentStore = is_null($store);
			if ($forCurrentStore && isset($cachedResult)) {
				$result = $cachedResult;
			}
			else {
				$result = ('admin' === df_store($store)->getCode());
				if ($forCurrentStore) {
					$cachedResult = $result;
				}
			}
			return $result;
		}
 * Однако мы не вправе кэшировать результат работы функции:
 * ведь текущий магазин может меняться. Поэтому убрал кэширование.
 * 2024-01-09 "Port `df_is_admin()`": https://github.com/thehcginstitute-com/m1/issues/144
 */
function df_is_admin($store = null) {return 'admin' === Mage::app()->getStore($store)->getCode();}

/**
 * @used-by df_context()
 * @used-by INT\DisplayCvv\B::_prepareSpecificInformation() (https://github.com/thehcginstitute-com/m1/issues/142)
 * @return string
 */
function df_current_url() {return df_mage_url_h()->getCurrentUrl();}

/**
 * 2016-05-15 http://stackoverflow.com/a/2053295
 * 2017-06-09 It intentionally returns false in the CLI mode.
 * @used-by df_my_local()
 * @return bool
 */
function df_is_localhost() {return in_array(dfa($_SERVER, 'REMOTE_ADDR', []), ['127.0.0.1', '::1']);}

/**
 * 2017-04-17
 * @return bool
 */
function df_my() {return isset($_SERVER['DF_DEVELOPER']);}

/**
 * 2017-06-09 «dfediuk» is the CLI user name on my localhost.
 * @used-by df_visitor_ip()
 * @return bool
 */
function df_my_local() {return dfcf(function() {return
	df_my() && (df_is_localhost() || 'dfediuk' === dfa($_SERVER, 'USERNAME'))
;});}