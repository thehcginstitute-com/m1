<?php
use Mage_Core_Model_Store as S;
/**
 * 2024-05-10 "Port `df_cfg()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/592
 * @used-by HCG\MailChimp\Batch\Subscriber::p()
 * @used-by HCG\MagePsycho\Helper::cfg()
 * @used-by hcg_mc_cfg_fields()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @param null|string|bool|int|S $s
 * @return mixed
 */
function df_cfg(string $k, $s = null) {return Mage::getStoreConfig($k, $s);}