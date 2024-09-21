<?php
use Df\Core\Exception as E;
use SimpleXMLElement as CX;

/**
 * @used-by df_leaf()
 * @throws E
 */
function df_assert_leaf(CX $e):CX {return df_check_leaf($e) ? $e : df_error(
	"Требуется лист XML, однако получена ветка XML:\n%s.", df_xml_report($e)
);}