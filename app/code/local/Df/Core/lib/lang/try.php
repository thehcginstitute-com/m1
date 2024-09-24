<?php
use Closure as F;
use Throwable as T; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
/**
 * 2017-04-15
 * @used-by df_area_code()
 * @used-by df_asset_url()
 * @used-by df_cms_block_get()
 * @used-by df_contents()
 * @used-by df_country_ctn()
 * @used-by df_currency_convert_safe()
 * @used-by df_customer()
 * @used-by df_customer_id()
 * @used-by df_customer_group_name()
 * @used-by df_date_from_db()
 * @used-by df_gd()
 * @used-by df_intl_dic_read()
 * @used-by df_layout_update()
 * @used-by df_magento_version_remote()
 * @used-by df_module_file_name()
 * @used-by df_phone()
 * @used-by df_product_att()
 * @used-by df_xml_x()
 * @used-by df_zuri()
 * @used-by Df\Qa\Trace\Formatter::p()
 * @used-by Df\Qa\Trace\Frame::methodR()
 * @used-by Df\Xml\G::addChild()
 * @param F|T|bool|mixed $onE [optional]
 * @return mixed
 * @throws T
 */
function df_try(F $try, $onE = null) {
	try {return $try();}
	catch(T $t) {return $onE instanceof F ? $onE($t) : (df_is_th($onE) || true === $onE ? df_error($t) : $onE);}
}