<?php
/**
 * 2015-08-14 Мы не вправе кэшировать результат работы функции: ведь текущий магазин может меняться.
 * 2015-09-20
 * В отличие от Magento 1.x мы не можем использовать код
 * Magento\Store\Model\Store::ADMIN_CODE === df_store($store)->getCode();
 * потому что при нахождении в административной части
 * он вернёт вовсе не административную витрину, а витрину, указанную в MAGE_RUN_CODE.
 * Более того, @see df_store() учитывает параметры URL
 * и даже при нахождении в административном интерфейсе
 * может вернуть вовсе не административную витрину.
 * Поэтому определяем нахождение в административном интерфейсе другим способом.
 * 2016-09-30
 * Используемые константы присутствуют уже в релизе 2.0.0, потому использовать их безопасно:
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/App/Area.php
 * 2016-12-23
 * Если мы обрабатываем асинхронный запрос к серверу, то @uses \Magento\Framework\App\State::getAreaCode()
 * вернёт не «adminhtml», а, например, «webapi_rest».
 * В то же время @uses df_backend_user() безопасно использовать даже с витрины.
 * 2024-03-17 "Port `df_is_backend()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/499
 * @see df_is_ajax()
 * @see df_is_frontend()
 * @see df_is_rest()
 * @used-by df_backend_user()
 * @used-by df_backend_user_id()
 * @used-by df_ban()
 * @used-by df_block()
 * @used-by df_catalog_locator()
 * @used-by df_customer_id()
 * @used-by df_product_current()
 * @used-by df_session()
 * @used-by df_store()
 */
function df_is_backend():bool {return 'admin' === Mage::app()->getStore()->getCode();}