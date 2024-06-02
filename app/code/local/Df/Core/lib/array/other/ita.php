<?php
/**
 * 2015-02-11
 * Эта функция отличается от @see iterator_to_array() тем, что допускает в качестве параметра
 * не только @see \Traversable, но и массив.
 * @param \Traversable|array $t
 * @return array
 */
function df_ita($t) {return is_array($t) ? $t : iterator_to_array($t);}