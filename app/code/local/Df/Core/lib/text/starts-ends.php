<?php

/**
 * Утверждают, что код ниже работает быстрее, чем return 0 === mb_strpos($haystack, $needle);
 * http://stackoverflow.com/a/10473026
 * http://stackoverflow.com/a/834355
 * 2022-10-14 @see str_starts_with() has been added to PHP 8: https://www.php.net/manual/function.str-starts-with.php
 * 2022-11-12 It returns `true` if $needle is an empty string: https://3v4l.org/R3WhEH
 * @see df_ends_with()
 * @see df_prepend()
 * @see df_trim_text_left()
 * @used-by df_action_prefix()
 * @used-by df_check_https()
 * @used-by df_check_json_complex()
 * @used-by df_check_url_absolute()
 * @used-by df_check_xml()
 * @used-by df_handle_prefix()
 * @used-by df_log_l()
 * @used-by df_modules_my()
 * @used-by df_modules_p()
 * @used-by df_package()
 * @used-by df_path_is_internal()
 * @used-by df_prepend()
 * @used-by df_starts_with()
 * @used-by df_zf_http_last_req()
 * @used-by \Df\Qa\Trace::__construct()
 * @param string $haystack
 * @param string|string[] $n
 * @return bool
 */
function df_starts_with($haystack, $n) {return is_array($n)
	? null !== df_find($n, __FUNCTION__, [], [$haystack])
	: $n === mb_substr($haystack, 0, mb_strlen($n))
;}