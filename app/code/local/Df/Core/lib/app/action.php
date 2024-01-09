<?php
/**
 * 2019-12-26
 * @see \Magento\Store\App\Response\Redirect::getRefererUrl():
 * 		df_response_redirect()->getRefererUrl()
 * 2023-01-28
 * «Return value of df_referer() must be of the type string, null returned»: https://github.com/mage2pro/core/issues/177
 * @used-by df_context()
 * @return string
 */
function df_referer() {return dfa($_SERVER, 'HTTP_REFERER', '');}