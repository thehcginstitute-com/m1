<?php
/**
 * 2021-01-29
 * 2022-11-27 Added the @uses df_nes() check.
 * @see dfa_strict()
 * @used-by df_cfg_save()
 * @used-by df_gender_s() (https://github.com/thehcginstitute-com/m1/issues/589)
 * @used-by df_url_bp()
 * @param int|string $v
 * @param array(int|string => mixed) $map
 * @return int|string|mixed
 */
function dftr($v, array $map) {return df_nes($v) ? $v : dfa($map, $v, $v);}