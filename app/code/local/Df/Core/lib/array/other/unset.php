<?php
/**
 * 2016-09-02
 * @see dfa_deep_unset()
 * @uses array_flip() корректно работает с пустыми массивами.
 * @param array(string => mixed) $a
 * @param string[] $keys
 * @return array(string => mixed)
 */
function dfa_unset(array $a, array $keys) {return array_diff_key($a, array_flip($keys));}