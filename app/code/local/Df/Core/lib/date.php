<?php
use Zend_Date as ZD;

/**
 * 2016-07-19
 * @used-by df_day_of_week_as_digit()
 * @used-by df_dts()
 * @used-by df_hour()
 * @used-by df_month()
 * @used-by df_num_days()
 * @used-by df_year()
 * @return ZD
 */
function df_date(ZD $d = null) {return $d ?: ZD::now();}

/**
 * 2015-02-07
 * 1) Обратите внимание, что в сигнатуре метода/функции для параметров объектного типа со значением по умолчанию null
 * мы вправе, тем не менее, указывать тип-класс.
 * Проверял на всех поддерживаемых Российской сборкой Magento версиях интерпретатора PHP, сбоев нет:
 * http://3v4l.org/ihOFp
 * 2) Несмотря на свою спецификацию, @uses ZD::toString() может вернуть не только строку, но и FALSE.
 * http://www.php.net/manual/en/function.date.php
 * https://php.net/gmdate
 * @used-by df_date_from_timestamp_14()
 * @used-by df_dtss()
 * @used-by df_file_name()
 * @param string|null $fmt [optional]
 * @param Zend_Locale|string|null $l [optional]
 * @return string
 */
function df_dts(ZD $d = null, $fmt = null, $l = null) {return df_date($d)->toString($fmt, null, $l);}