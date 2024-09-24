<?php
namespace Df\Xml;
use Df\Core\Exception as E;
use \SimpleXMLElement as X;
use \Throwable as T; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
# 2024-09-22
# 1) "Refactor `Df\Xml\X`": https://github.com/mage2pro/core/issues/436
# 2) "Refactor the `Df_Xml` module": https://github.com/mage2pro/core/issues/437
final class G {
	/**
	 * 2024-09-23
	 * @override
	 * @see Stringable::__toString()
	 * @used-by df_xml_g()
	 */
	function __toString():string {return df_xml_s($this->_x);}

	/** @used-by self::importArray() */
	const ATTR = '_attr';

	/** @used-by self::importArray() */
	const CONTENT = '_content';

	/**
	 * @used-by df_xml_go()
	 * @param array(string => string) $attr [optional]
	 * @param array(string => mixed) $contents [optional]
	 */
	static function i(string $tag, array $attr = [], array $contents = []):self {
		$r = new self(df_xml_x("<{$tag}/>")); /** @var self $r */
		$r->addAttributes($attr);
		$r->importArray($contents);
		return $r;
	}

	/**
	 * 2024-09-22
	 * @used-by self::i()
	 * @used-by self::addChild()
	 * @param X|string $x
	 */
	private function __construct($x) {$this->_x = df_xml_x($x);}

	/**
	 * 2021-12-13
	 * @used-by self::addAttributes()
	 * @used-by self::addChildX()
	 */
	private function addAttribute(string $k, string $v = '', string $ns = ''):void {
		$this->_x->addAttribute($this->k($k), $v, $ns);
	}

	/**
	 * @used-by self::i()
	 * @used-by self::importArray()
	 * @param array(string => string) $atts
	 */
	private function addAttributes(array $aa):void {
		foreach ($aa as $k => $v) {/** @var string $k */ /** @var mixed $v */
			$this->addAttribute(df_assert_sne($k), df_assert_stringable(
				$v, sprintf("The attribute «{$k}» has a non-`Stringable` type %s.", df_type($v)), ['attributes' => $aa]
			));
		}
	}

	/**
	 * @used-by self::i()
	 * @used-by self::importArray()
	 * @param array(string => mixed) $array
	 * @param string[]|bool $wrapInCData [optional]
	 */
	private function importArray(array $array, $wrapInCData = []):void {
		foreach ($array as $key => $v) { /** @var string $key */ /** @var mixed $v */
			if ($v instanceof self) {
				/**
				 * 2016-08-31
				 * Случай, который отсутствовал в Российской сборке Magento:
				 *	'Payment' => [
				 *		df_xml_go('TxnList', ['count' => 1], [
				 *			df_xml_go('Txn', ['ID' => 1], [
				 *				'amount' => 200
				 *				,'purchaseOrderNo' => 'test'
				 *				,'txnID' => '009887'
				 *				,'txnSource' => 23
				 *				,'txnType' => 4
				 *			])
				 *		])
				 *	]
				 *	<Payment>
				 *		<TxnList count="1">
				 *			<Txn ID="1">
				 *				<txnType>4</txnType>
				 *				<txnSource>23</txnSource>
				 *				<amount>200</amount>
				 *				<purchaseOrderNo>test</purchaseOrderNo>
				 *				<txnID>009887</txnID>
				 *			</Txn>
				 *		</TxnList>
				 *	</Payment>
				 */
				$this->addChildX($v);
			}
			elseif (!is_array($v)) {
				$this->importString($key, $v, $wrapInCData);
			}
			elseif (df_is_assoc($v) || array_filter($v, function($i) {return $i instanceof self;})) {
				$childNode =
					$this->addChild(
						/**
						 * Раньше тут стояло df_string($key)
						 * Для ускорения модуля Яндекс.Маркет @see df_string() убрал.
						 * Вроде ничего не ломается.
						 */
						$key
					)
				; /** @var self $childNode */
				$childData = $v; /** @var array|null $childData */
				# Данный программный код позволяет импортировать атрибуты тэгов
				/** @var array(string => string)|null $attributes $attributes */
				$attributes = dfa($v, self::ATTR);
				if (!is_null($attributes)) {
					$childNode->addAttributes(df_assert_array($attributes));
					# Если $v содержит атрибуты,
					# то дочерние значения должны содержаться не непосредственно в $v, а в подмассиве с ключём self::CONTENT.
					$childData = dfa($v, self::CONTENT);
				}
				if (!is_null($childData)) {
					/**
					 * $childData запросто может не быть массивом.
					 * Например, в такой ситуации:
					 *	(
					 *		[_attributes] => Array
					 *			(
					 *				[Код] => 796
					 *				[НаименованиеПолное] => Штука
					 *				[МеждународноеСокращение] => PCE
					 *			)
					 *		[_value] => шт
					 *	)
					 * Здесь $childData — это «шт».
					 */
					if (is_array($childData)) {
						$childNode->importArray($childData, $wrapInCData);
					}
					else {
						# '' означает, что метод importString() не должен создавать дочерний тэг $key,
						# а должен добавить текст в качестве единственного содержимого текущего тэга.
						$childNode->importString('', $childData, $wrapInCData);
					}
				}
			}
			else {
				# Данный код позволяет импортировать структуры с повторяющимися тегами.
				# Например, нам надо сформировать такой документ:
				#	<АдресРегистрации>
				#		<АдресноеПоле>
				#			<Тип>Почтовый индекс</Тип>
				#			<Значение>127238</Значение>
				#		</АдресноеПоле>
				#		<АдресноеПоле>
				#			<Тип>Улица</Тип>
				#			<Значение>Красная Площадь</Значение>
				#		</АдресноеПоле>
				#	</АдресРегистрации>
				#
				# Для этого мы вызываем:
				#
				#	$this->getDocument()
				#		->importArray(
				#			array(
				#				'АдресРегистрации' =>
				#					array(
				#						'АдресноеПоле' =>
				#							array(
				#								array(
				#									'Тип' => 'Почтовый индекс'
				#									,'Значение' => '127238'
				#								)
				#								,array(
				#									'Тип' => 'Улица'
				#									,'Значение' => 'Красная Площадь'
				#								)
				#							)
				#					)
				#			)
				#		)
				#	;
				foreach ($v as $vItem) {
					/** @var array(string => mixed)|string $vItem */
					/**
					 * 2015-01-20
					 * Обратите внимание, что $vItem может быть не только массивом, но и строкой.
					 * Например, такая структура:
					 *	<Группы>
					 *		<Ид>1</Ид>
					 *		<Ид>1</Ид>
					 *		<Ид>1</Ид>
					 *	</Группы>
					 * кодируется так:
					 * array('Группы' => array('Ид' => array(1, 2, 3)))
					 */
					$this->importArray([$key => $vItem], $wrapInCData);
				}
			}
		}
	}

	/**
	 * @uses \SimpleXMLElement::addChild() создаёт и возвращает не просто SimpleXMLElement, как говорит документация,
	 * а объект класса родителя.
	 * 2022-11-15
	 * 1) `static` as a return type is not supported by PHP < 8: https://github.com/mage2pro/core/issues/168#user-content-static
	 * 2) We can not declare the $name argument type with PHP < 8: https://3v4l.org/ptpUM
	 * @used-by self::addChildText()
	 * @used-by self::addChildX()
	 * @used-by self::importArray()
	 * @used-by self::importString()
	 * @throws E
	 */
	private function addChild(string $k, string $v = '', string $ns = ''):self {return df_try(
		function() use($k, $v, $ns):self {return new self($this->_x->addChild($this->k($k), $v, $ns));}
		,function(T $t) use($k, $v):void {df_error("Tag <{$k}>. Value: «{$v}». Error: «%s».", df_xts($t));}
	);}

	/** @used-by self::importString() */
	private function addChildText(string $tag, string $s):void {
		$r = $this->addChild($tag); /** @var self $r */
		$r->cdata($s);
	}

	/**
	 * 2016-08-31 http://stackoverflow.com/a/11727581
	 * @used-by self::addChildX()
	 * @used-by self::importArray()
	 */
	private function addChildX(X $x):void {
		$g = $this->addChild($x->getName(), (string)$x); /** @var self $g */
		foreach ($x->attributes() as $k => $v) { /** @var string $k */ /** @var string $v */
			$g->addAttribute($k, $v);
		}
		foreach ($x->children() as $xc) { /** @var X $xc */
			$g->addChildX($xc);
		}
	}

	/**
	 * http://stackoverflow.com/a/6260295
	 * @used-by self::addChildText()
	 * @used-by self::importString()
	 */
	private function cdata(string $s):void {
		$e = dom_import_simplexml($this->_x); /** @var \DOMElement $e */
		$e->appendChild($e->ownerDocument->createCDATASection($s));
	}

	/**
	 * @used-by self::importArray()
	 * @param mixed $v
	 * @param string[]|bool $wrapInCData [optional]
	 */
	private function importString(string $k, $v, $wrapInCData = []):void {
		$needWrapInCData = !is_array($wrapInCData) && !!$wrapInCData; /** @var bool $needWrapInCData */
		$wrapInCData = df_eta($wrapInCData);
		# '' означает, что метод `importString` не должен создавать дочерний тэг `$k`,
		# а должен добавить текст в качестве единственного содержимого текущего тэга.
		$kIsEmpty = df_es($k); /** @var bool $kIsEmpty */
		$kAsString = $kIsEmpty ? $this->_x->getName() : $k; /** @var string $kAsString */
		$vIsString = is_string($v); /** @var bool $vIsString */
		$vAsString = ''; /** @var string $vAsString */
		try {$vAsString = $vIsString ? $v : df_string($v);}
		catch (T $t) {df_error("Unable to convert the value of the key «{$kAsString}» to a string.\n%s", df_xts($t));}
		if ($vIsString && $vAsString) {
			/**
			 * Поддержка синтаксиса
			 *	 [
			 *		'Представление' =>
			 *			df_cdata($this->getAddress()->format(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_TEXT))
			 *	 ]
			 * Обратите внимание, что проверка на синтаксис[[]] должна предшествовать
			 * проверке на принадлежность ключа $kAsString в массиве $wrapInCData,
			 * потому что при соответствии синтаксису[[]] нам надо удалить из значения символы[[]].
			 * Обратите внимание, что нам нужно выполнить проверку на синтаксис df_cdata ([[]])
			 * даже при $needWrapInCData = true, потому что маркеры [[ и ]] из данных надо удалять.
			 */
			if (df_cdata_m()->marked($vAsString)) {
				$vAsString = df_cdata_m()->unmark($vAsString);
				$needWrapInCData = true;
			}
			$needWrapInCData = $needWrapInCData || in_array($kAsString, $wrapInCData) || df_needs_cdata($vAsString);
		}
		$needWrapInCData
			? ($kIsEmpty ? $this->cdata($vAsString) : $this->addChildText($kAsString, $vAsString))
			: (
				$kIsEmpty
				? $this->_x[0] = $vAsString # http://stackoverflow.com/a/3153704
				: $this->addChild(
					$kAsString
					/**
					 * Обратите внимание, что мы намеренно не добавляем htmlspecialchars:
					 * пусть вместо этого источник данных помечает те даннные, которые
					 * могут содержать неразрешённые в качестве содержимого тегов XML
					 * значения посредством @see df_cdata()
					 */
					,$vAsString
				)
			)
		;
	}

	/**
	 * 2021-12-16
	 * https://stackoverflow.com/a/9391673
	 * https://stackoverflow.com/a/43566078
	 * https://stackoverflow.com/a/6928183
	 * @used-by self::addAttribute()
	 * @used-by self::addChild()
	 */
	private function k(string $s):string {return !df_contains($s, ':') ? $s : "xmlns:$s";}

	/**
	 * 2024-09-22
	 * @used-by self::__construct()
	 * @used-by self::__toString()
	 * @used-by self::addAttribute()
	 * @used-by self::addChild()
	 * @used-by self::cdata()
	 * @used-by self::importString()
	 * @var X
	 */
	private $_x;
}