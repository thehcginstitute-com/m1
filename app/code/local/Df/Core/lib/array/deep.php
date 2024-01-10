<?php
/**
 * Этот метод предназначен для извлечения некоторого значения
 * из многомерного массива посредством нотации 'ключ1/ключ2/ключ3'.
 * Например: dfa_deep(['test' => array('eee' => 3)], 'test/eee') вернёт «3».
 * Ядро Magento реализует аналогичный алгоритм в методе @see \Magento\Framework\DataObject::getData()
 * Наша функция работает не только с объектами @see \Magento\Framework\DataObject, но и с любыми массивами.
 * 2017-03-28
 * Сегодня заметил, что успешно работают пути типа 'transactions/0'
 * в том случае, когда ключ верхнего уровня возвращает массив с целочисленными индексами.
 * 2022-11-27 dfa_deep(['a' => ['b' => 3]], ['a', null]) will return ['b' => 3]. @see df_cli_argv()
 * @used-by dfa()
 * @param array(string => mixed) $a
 * @param string|string[] $path
 * @param mixed $d [optional]
 * @return mixed|null
 */
function dfa_deep(array $a, $path = '', $d = null) {/** @var mixed|null $r */ /** @var string[] $pathParts */
	if (df_nes($path)) {
		$r = $a;
	}
	elseif (is_array($path)) {
		$pathParts = $path;
	}
	else {
		if (isset($a[$path])) {
			$r = $a[$path];
		}
		else {
			/**
			 * 2015-02-06
			 * Обратите внимание, что если разделитель отсутствует в строке,
			 * то @uses explode() вернёт не строку, а массив со одим элементом — строкой.
			 * Это вполне укладывается в наш универсальный алгоритм.
			 */
			$pathParts = df_explode_xpath($path);
		}
	}
	if (!isset($r)) {
		$r = null;
		while ($pathParts) {
			$r = dfa($a, array_shift($pathParts));
			if (is_array($r)) {
				$a = $r;
			}
			else {
				if ($pathParts) {
					$r = null; # Ещё не прошли весь путь, а уже наткнулись на не-массив.
				}
				break;
			}
		}
	}
	return is_null($r) ? $d : $r;
}

/**
 * 2015-12-07
 * 2024-01-10 "Port `dfa_deep_set` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/156
 * @used-by \Df\Core\O::offsetSet()
 * @param array(string => mixed) $array
 * @param string|string[] $path
 * @param mixed $value
 * @return array(string => mixed)
 */
function dfa_deep_set(array &$array, $path, $value):array {
	$pathParts = df_explode_xpath($path); /** @var string[] $pathParts */
	$a = &$array; /** @var array(string => mixed) $a */
	while ($pathParts) {
		$key = array_shift($pathParts); /** @var string $key */
		if (!isset($a[$key])) {
			$a[$key] = [];
		}
		$a = &$a[$key];
		if (!is_array($a)) {
			$a = [];
		}
	}
	$a = $value;
	return $array;
}