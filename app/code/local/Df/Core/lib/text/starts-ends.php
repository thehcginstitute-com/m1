<?php
/**
 * http://stackoverflow.com/a/10473026
 * http://stackoverflow.com/a/834355
 * @see df_append()
 * @see df_starts_with()
 * @see df_trim_text_right()
 * 2022-10-14 @see str_ends_with() has been added to PHP 8: https://php.net/manual/function.str-ends-with.php
 * @used-by df_append()
 * @used-by df_cc_path()
 * @used-by df_ends_with()
 * @used-by df_is_bin_magento()
 * @used-by df_is_phtml()
 * @used-by df_referer_ends_with()
 * @used-by Df\Core\Text\Marker::marked()
 * @used-by Df\Core\Text\Regex::getErrorCodeMap()
 * @used-by Df\Qa\Trace\Frame::isClosure()
 * @used-by Df\Zf\Validate\StringT\FloatT::isValid()
 * @param string|string[] $n
 */
function df_ends_with(string $haystack, $n):bool {return is_array($n)
	? null !== df_find($n, __FUNCTION__, [], [$haystack])
	: 0 === ($l = mb_strlen($n)) || $n === mb_substr($haystack, -$l)
;}

/**
 * Утверждают, что код ниже работает быстрее, чем return 0 === mb_strpos($haystack, $needle);
 * http://stackoverflow.com/a/10473026
 * http://stackoverflow.com/a/834355
 * 2022-10-14 @see str_starts_with() has been added to PHP 8: https://php.net/manual/function.str-starts-with.php
 * 2022-11-12 It returns `true` if $needle is an empty string: https://3v4l.org/R3WhEH
 * @see df_ends_with()
 * @see df_prepend()
 * @see df_trim_text_left()
 * @used-by df_action_prefix()
 * @used-by df_caller_entry_m()
 * @used-by df_cc_path()
 * @used-by df_check_https()
 * @used-by df_check_json_complex()
 * @used-by df_is_xml()
 * @used-by df_handle_prefix()
 * @used-by df_is_url_absolute()
 * @used-by df_log_l()
 * @used-by df_module_name_by_path()
 * @used-by df_modules_my()
 * @used-by df_modules_p()
 * @used-by df_path_abs()
 * @used-by df_path_is_internal()
 * @used-by df_prepend()
 * @used-by df_starts_with()
 * @used-by df_zf_http_last_req()
 * @used-by Df\Core\Text\Marker::marked()
 * @used-by Df\Framework\Request::extraKeysRaw()
 * @used-by Df\Qa\Trace::__construct()
 * @used-by Df\Zf\Validate\StringT\IntT::isValid()
 * @param string|string[] $n
 */
function df_starts_with(string $haystack, $n):bool {return is_array($n)
	? null !== df_find($n, __FUNCTION__, [], [$haystack])
	: $n === mb_substr($haystack, 0, mb_strlen($n))
;}