<?php
/**
 * 2016-05-20
 * It returns the country name name for an ISO 3166-1 alpha-2 2-characher code and locale
 * (or the default system locale) given: https://ru.wikipedia.org/wiki/ISO_3166-1
 * 2024-03-31 "Port `df_country_ctn()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/536
 */
function df_country_ctn(string $iso2):string {df_param_iso2($iso2, 0); return
	dfa(df_countries_ctn(), strtoupper($iso2)) ?: df_error(
		'Unable to find out the name of the country with the ISO code «%1»».', $iso2
	)
;}