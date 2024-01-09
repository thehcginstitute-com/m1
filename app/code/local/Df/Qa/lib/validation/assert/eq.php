<?php
use Exception as E;
/**
 * @used-by df_action_name()
 * @used-by df_contents()
 * @used-by df_file_name()
 * @used-by df_json_decode()
 * @param string|int|float|bool $neResult
 * @param string|int|float|bool $v
 * @param string|E|null $m [optional]
 * @return string|int|float|bool
 * @throws E
 */
function df_assert_ne($neResult, $v, $m = null) {return $neResult !== $v ? $v : df_error($m ?:
	"The value $v is rejected, any other is allowed."
);}