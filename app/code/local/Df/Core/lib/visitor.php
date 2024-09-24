<?php
/**
 * @used-by df_context()
 * @used-by df_visitor_ip()
 */
function df_visitor_ip():string {return df_my_local() ? '158.181.235.66' : dfa(
	# 2021-06-11
	# 1) «Ensure that the Customer IP address is being passed in the API request for all transactions»:
	# https://github.com/canadasatellite-ca/site/issues/175
	# 2) https://stackoverflow.com/a/14985633
	$_SERVER, 'HTTP_CF_CONNECTING_IP', df_mage_http_h()->getRemoteAddr()
);}