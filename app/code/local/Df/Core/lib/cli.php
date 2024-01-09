<?php
/**
 * 2017-03-15 Нулевой параметр argv — это имя текущего скрипта.
 * 2022-11-23 With $i = null the function returns all `argv` data: @see df_cli_cmd().
 * 2023-01-27
 * The previous code was: `dfa_deep($_SERVER, ['argv', $i])`.
 * It did not handle correctly the non-CLI case (returned `null`):
 * «ju_cli_script(): Return value must be of type string»: https://github.com/justuno-com/core/issues/384
 * @used-by df_cli_cmd()
 * @used-by df_cli_script()
 * @used-by df_is_cron()
 * @param int|null $i [optional]
 * @return string|string[]
 */
function df_cli_argv($i = null) {return dfa(dfa($_SERVER, 'argv', []), $i, '');}

/**
 * 2020-05-24
 * @used-by df_context()
 * @return string
 */
function df_cli_cmd() {return df_cc_s(df_cli_argv());}

/**
 * 2016-10-25 http://stackoverflow.com/a/1042533
 * @used-by df_context()
 * @return bool
 */
function df_is_cli() {return 'cli' === php_sapi_name();}