<?php
use Df\Xml\G;

/**
 * @used-by df_xml_g()
 * @param array(string => string) $attr [optional]
 * @param array(string => mixed) $contents [optional]
 */
function df_xml_go(string $tag, array $attr = [], array $contents = []):G {return G::i($tag, $attr, $contents);}