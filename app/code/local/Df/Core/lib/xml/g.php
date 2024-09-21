<?php
use Df\Xml\X;

/**
 * @see \Magento\Framework\Simplexml\Element::asNiceXml() не добавляет к документу заголовок XML: его надо добавить вручную.
 * 2015-02-27
 * Для конвертации объекта класса @see SimpleXMLElement в строку
 * надо использовать именно метод @uses SimpleXMLElement::asXML(),
 * а не @see SimpleXMLElement::__toString() или оператор (string)$this.
 * @see SimpleXMLElement::__toString() и (string)$this
 * возвращают непустую строку только для концевых узлов (листьев дерева XML).
 * Пример:
 *	<?xml version='1.0' encoding='utf-8'?>
 *		<menu>
 *			<product>
 *				<cms>
 *					<class>aaa</class>
 *					<weight>1</weight>
 *				</cms>
 *				<test>
 *					<class>bbb</class>
 *					<weight>2</weight>
 *				</test>
 *			</product>
 *		</menu>
 * Здесь для $e1 = $xml->{'product'}->{'cms'}->{'class'}
 * мы можем использовать $e1->__toString() и (string)$e1.
 * http://3v4l.org/rAq3F
 * Однако для $e2 = $xml->{'product'}->{'cms'}
 * мы не можем использовать $e2->__toString() и (string)$e2,
 * потому что узел «cms» не является концевым узлом (листом дерева XML): http://3v4l.org/Pkj37
 * Более того, метод @see SimpleXMLElement::__toString() отсутствует в PHP версий 5.2.17 и ниже:
 * http://3v4l.org/Wiia2#v500
 * 2016-08-31 Портировал из Российской сборки Magento.
 * 2022-11-15
 * 1) https://github.com/mage2pro/core/blob/2.0.0/Xml/G.php?ts=4
 * 2) $skipHeader is not used currently.
 * @param array(string => mixed) $contents [optional]
 * @param array(string => mixed) $p [optional]
 */
function df_xml_g(string $tag, array $contents = [], array $atts = [], bool $skipHeader = false):string {
	$h = $skipHeader ? '' : df_xml_header(); /** @var string $h */
	$x = df_xml_parse(df_cc_n($h, "<{$tag}/>")); /** @var X $x */
	$x->addAttributes($atts);
	$x->importArray($contents);
	# Символ 0xB (вертикальная табуляция) допустим в UTF-8, но недопустим в XML: http://stackoverflow.com/a/10095901
	return str_replace("\x0B", "&#x0B;", $skipHeader ? $x->asXMLPart() : df_cc_n($h, $x->asNiceXml()));
}

/**
 * @param array(string => string) $attr [optional]
 * @param array(string => mixed) $contents [optional]
 */
function df_xml_node(string $tag, array $attr = [], array $contents = []):X {
	$r = df_xml_parse("<{$tag}/>"); /** @var X $r */
	$r->addAttributes($attr);
	$r->importArray($contents);
	return $r;
}