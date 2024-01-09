<?php
/**
 * @used-by df_context()
 * @return string
 */
function df_current_url() {return df_mage_url_h()->getCurrentUrl();}

/**
 * 2016-05-15 http://stackoverflow.com/a/2053295
 * 2017-06-09 It intentionally returns false in the CLI mode.
 * @used-by df_my_local()
 * @return bool
 */
function df_is_localhost() {return in_array(dfa($_SERVER, 'REMOTE_ADDR', []), ['127.0.0.1', '::1']);}

/**
 * 2017-04-17
 * @return bool
 */
function df_my() {return isset($_SERVER['DF_DEVELOPER']);}

/**
 * 2017-06-09 «dfediuk» is the CLI user name on my localhost.
 * @used-by df_visitor_ip()
 * @return bool
 */
function df_my_local() {return dfcf(function() {return
	df_my() && (df_is_localhost() || 'dfediuk' === dfa($_SERVER, 'USERNAME'))
;});}