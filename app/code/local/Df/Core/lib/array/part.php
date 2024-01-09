<?php
/**
 * 2021-10-05, 2021-11-30
 * @uses array_slice() returns an empty array if `$limit` is `0`, and returns all elements if `$limit` is `null`,
 * so I convert `0` and other empty values to `null`.
 * @used-by df_bt()
 * @used-by df_product_images_additional()
 * @param int $offset
 * @param int $length [optional]
 * @return array
 */
function df_slice(array $a, $offset, $length = 0) {return array_slice($a, $offset, df_etn($length));}