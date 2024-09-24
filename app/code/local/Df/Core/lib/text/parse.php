<?php
/**
 * 2018-09-27
 * @return array(string => string)
 */
function df_parse_colon(string $s):array {return df_map_r(df_explode_n($s), function(string $s):array {return df_trim(
	explode(':', $s)
);});}