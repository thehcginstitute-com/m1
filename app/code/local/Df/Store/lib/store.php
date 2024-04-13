<?php
use Mage_Core_Model_Store as S;
use Mage_Sales_Model_Order as O;
/**
 * 2015-02-04
 * Обратите внимание, что вряд ли мы вправе кэшировать результат при парметре $store = null,
 * ведь текущий магазин может меняться.
 * 2015-08-10
 * Доработал алгоритм.
 * Сначала мы смотрим, не находимся ли мы в административной части,
 * и нельзя ли при этом узнать текущий магазин из веб-адреса.
 * By analogy with @see Mage_Adminhtml_Block_Catalog_Product_Grid::_getStore()
 * 2015-11-04
 * 1) By analogy with @see \Magento\Store\Model\StoreResolver::getCurrentStoreId()
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Store/Model/StoreResolver.php#L82
 * 2.1) При нахождении в административном интерфейсе и при отсутствии в веб-адресе идентификатора магазина
 * этот метод вернёт витрину по-умолчанию, а не витрину «admin».
 * Не знаю, правильно ли это, но так делает этот метод в Российской сборке для Magento 1.x,
 * поэтому решил пока не менять поведение.
 * 2.2) В Magento 2 стандартный метод \Magento\Store\Model\StoreManager::getStore()
 * при вызове без параметров возвращает именно витрину по умолчанию, а не витрину «admin»:
 * https://github.com/magento/magento2/issues/2254
 * «The call for \Magento\Store\Model\StoreManager::getStore() without parameters
 * inside the backend returns the default frontend store, not the «admin» store,
 * which is inconsistent with Magento 1.x behavior and I think it will lead to developer mistakes.»
 * 2024-04-14 "Port `df_store()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/561
 * @used-by df_address_store()
 * @used-by df_currencies_codes_allowed()
 * @used-by df_currencies_ctn()
 * @used-by df_currency_current()
 * @used-by df_media_path2url()
 * @used-by df_media_url2path()
 * @used-by df_scope_stores()
 * @used-by df_store_country()
 * @used-by df_store_id()
 * @used-by df_store_url()
 * @used-by df_url_frontend()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getRealScopeForConfig() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @param int|string|null|bool|S|O $v [optional]
 * @throws Exception
 * https://github.com/magento/magento2/issues/2222
 */
function df_store($v = null):S {/** @var string|null $c */return
	df_is_o($v) ? $v->getStore() : (is_object($v) ? $v : Mage::app()->getStore($v ?: (
		!is_null($c = df_request('store')) ? $c : (
			# 2017-08-02
			# The store ID specified in the current URL should have priority over the value from the cookie.
			# Violating this rule led us to the following failure:
			# https://github.com/mage2pro/markdown/issues/1
			# Today I was saving a product in the backend, the URL looked like:
			# https://site.com/admin/catalog/product/save/id/45/type/simple/store/0/set/20/key/<key>/back/edit
			# But at the same time I had another store value in the cookie (a frontend store code).
			!is_null($c = df_request('store-view')) ? $c : (
				df_is_backend() ? df_request('store', 'admin') : null
			)
		)
	)))
;}