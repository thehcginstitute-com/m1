<?php
/**
 * 2016-03-08 It adds the $tail suffix to the $s string if the suffix is absent in $s.
 * @used-by df_cc_path_t()
 * @used-by df_file_ext_add()
 */
function df_append(string $s, string $tail):string {return df_ends_with($s, $tail) ? $s : $s . $tail;}

/**
 * Аналог @see str_pad() для Unicode: http://stackoverflow.com/a/14773638
 * @used-by df_kv()
 * @used-by \Dfe\Moip\CardFormatter::label()
 * @param string $phrase
 * @param int $length
 * @param string $pattern
 * @param int $position
 * @return string
 */
function df_pad($phrase, $length, $pattern = ' ', $position = STR_PAD_RIGHT) {/** @var string $r */
	$encoding = 'UTF-8'; /** @var string $encoding */
	$input_length = mb_strlen($phrase, $encoding); /** @var int $input_length */
	$pad_string_length = mb_strlen($pattern, $encoding); /** @var int $pad_string_length */
	if ($length <= 0 || $length - $input_length <= 0) {
		$r = $phrase;
	}
	else {
		$num_pad_chars = $length - $input_length; /** @var int $num_pad_chars */
		/** @var int $left_pad */ /** @var int $right_pad */
		switch ($position) {
			case STR_PAD_RIGHT:
				# 2024-06-06 "Use the «Symmetric array destructuring» PHP 7.1 feature": https://github.com/mage2pro/core/issues/379
				[$left_pad, $right_pad] = [0, $num_pad_chars];
				break;
			case STR_PAD_LEFT:
				# 2024-06-06 "Use the «Symmetric array destructuring» PHP 7.1 feature": https://github.com/mage2pro/core/issues/379
				[$left_pad, $right_pad] = [$num_pad_chars, 0];
				break;
			case STR_PAD_BOTH:
				$left_pad = floor($num_pad_chars / 2);
				$right_pad = $num_pad_chars - $left_pad;
				break;
			default:
				df_error();
		}
		$r = '';
		for ($i = 0; $i < $left_pad; ++$i) {
			$r .= mb_substr($pattern, $i % $pad_string_length, 1, $encoding);
		}
		$r .= $phrase;
		for ($i = 0; $i < $right_pad; ++$i) {
			$r .= mb_substr($pattern, $i % $pad_string_length, 1, $encoding);
		}
	}
	return $r;
}

/**
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * @used-by df_tab_multiline()
 * @param string|string[] $a
 * @return string|string[]|array(string => string)
 * @param string $s
 * @return string
 */
function df_tab(...$a) {return df_call_a(function($s) {return "\t" . $s;}, $a);}

/**
 * @used-by \Df\Qa\Dumper::dumpArray()
 * @used-by \Df\Qa\Dumper::dumpObject()
 * @param string $s
 * @return string
 */
function df_tab_multiline($s) {return df_cc_n(df_tab(df_explode_n($s)));}