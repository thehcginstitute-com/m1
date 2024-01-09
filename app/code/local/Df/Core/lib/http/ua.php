<?php
/**
 * 2016-12-25
 * 2017-02-18 Модуль Checkout.com раньше использовал dfa($_SERVER, 'HTTP_USER_AGENT')
 * @used-by df_context()
 * @used-by df_is_google_page_speed()
 * @used-by df_is_google_ua()
 * @used-by vendor/emipro/socialshare/view/frontend/templates/socialshare.phtml (dxmoto.com)
 * https://github.com/dxmoto/site/issues/103
 * @return string|bool
 */
function df_request_ua(...$s) {
	$r = df_request_header('user-agent'); /** @var string $r */
	return !$s ? $r : df_contains($r, ...$s);
}