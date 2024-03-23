<?php
/**
 * 2015-11-03
 * 2024-03-23 "Port `df_fetch_one()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/525
 * @used-by df_fetch_one_int()
 * @used-by \Alignet\Paymecheckout\Model\Client\Classic\Order\DataGetter::userCodePayme() (innomuebles.com, https://github.com/innomuebles/m2/issues/17)
 * @used-by \Dfe\CheckoutCom\Handler\Charge::paymentByTxnId()
 * @used-by \Dfe\Markdown\DbRecord::__construct()
 * @used-by \Inkifi\Consolidation\Processor::mcid()
 * @param string|string[] $cols
 * @param array(string => string) $compare
 * @return string|null|array(string => mixed)
 */
function df_fetch_one(string $t, $cols, array $compare) {
	$s = df_db_from($t, $cols); /** @var S $s */
	foreach ($compare as $c => $v) {/** @var string $c */ /** @var string $v */
		$s->where('? = ' . $c, $v);
	}
	/**
	 * 2016-03-01
	 * @uses \Zend_Db_Adapter_Abstract::fetchOne() возвращает false при пустом результате запроса.
	 * https://mage2.pro/t/853
	 */
	return '*' !== $cols ? df_ftn(df_conn()->fetchOne($s)) : df_eta(df_conn()->fetchRow($s, [], \Zend_Db::FETCH_ASSOC));
}