<?php
use Df\Core\Exception as E;
use Df\Xml\X;
use Throwable as T;

/**
 * @used-by df_module_name_by_path()
 * @used-by df_xml_g()
 * @used-by df_xml_node()
 * @used-by df_xml_parse_a()
 * @used-by df_xml_prettify()
 * @used-by df_xml_x()
 * @param X|string $x
 * @throws E
 */
function df_xml_parse($x, bool $throw = true):?X {/** @var ?X $r */
	if ($x instanceof X) {
		$r = $x;
	}
	else {
		df_param_sne($x, 0);
		$r = null;
		try {$r = new X($x);}
		catch (T $t) {
			if ($throw) {
				df_error(
					"Failed to parse an XML document:\n"
					. "«%s»\n"
					. "********************\n"
					. "%s\n"
					. "********************\n"
					, df_xts($t)
					, df_trim($x)
				);
			}
		}
	}
	return $r;
}

/**
 * 2018-12-19
 * @uses Varien_Simplexml_Element::asArray() returns XML tag's attributes
 * inside an `@` key, e.g:
 *	<authorizationResponse reportGroup="1272532" customerId="admin@mage2.pro">
 *		<litleTxnId>82924701437133501</litleTxnId>
 *		<orderId>f838868475</orderId>
 *		<response>000</response>
 *		<...>
 *	</authorizationResponse>
 * will be converted to:
 * 	{
 *		"@": {
 *			"customerId": "admin@mage2.pro",
 *			"reportGroup": "1272532"
 *		},
 *		"litleTxnId": "82924701437133501",
 *		"orderId": "f838868475",
 *		"response": "000",
 * 		<...>
 *	}
 * @used-by Mage_Core_Model_Layout::_generateAction() (https://github.com/thehcginstitute-com/m1/issues/684)
 * @param string|X $x
 * @return array(string => mixed)
 * @throws E
 */
function df_xml_parse_a($x):array {return df_xml_parse($x)->asArray();}

/**
 * 2016-09-01
 * Если XML не отформатирован, то после его заголовка перенос строки идти не обязан: http://stackoverflow.com/a/8384602
 * @used-by df_xml_prettify()
 * @param string|X $x
 * @return string|null
 */
function df_xml_parse_header($x) {return df_preg_match('#^<\?xml.*\?>#', df_xml_s($x));}

/**
 * 2016-09-01
 * 2021-12-02 @deprecated It is unused.
 * @see df_xml_s()
 * @param string|X $x
 */
function df_xml_x($x):X {return $x instanceof X ? $x : df_xml_parse($x);}