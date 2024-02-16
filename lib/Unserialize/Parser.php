<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category    Unserialize
 * @package     Unserialize_Parser
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Unserialize_Parser
 */
class Unserialize_Parser
{
    const TYPE_STRING = 's';
    const TYPE_INT = 'i';
    const TYPE_DOUBLE = 'd';
    const TYPE_ARRAY = 'a';
    const TYPE_BOOL = 'b';
    const TYPE_NULL = 'N';

    const SYMBOL_QUOTE = '"';
    const SYMBOL_SEMICOLON = ';';
    const SYMBOL_COLON = ':';

    /**
     * @param $str
     * @return array|null
     * @throws Exception
     */
    public function unserialize($str)
    {
		#  2024-01-22 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# 1) "«Error during unserialization in lib/Unserialize/Parser.php:59» on a customer login":
		# https://github.com/thehcginstitute-com/m1/issues/18
		# 2) "«Error during unserialization in lib/Unserialize/Parser.php:60» on the `/admin/system_config/` page":
		# https://github.com/thehcginstitute-com/m1/issues/298
		# 3) On the `/admin/system_config/` a $str is an instance of `Mage_Core_Model_Config_Element`,
		# and `(string)$str` is evaluated to «a:0:{}» (an empty array).
		# But the Magento's built-in parser can not parse it properly.
		# 4) "How did I fix «Error during unserialization in lib/Unserialize/Parser.php» in Magento 1.9?":
		# https://mage2.pro/t/6394
		$str = (string)$str;
        $reader = new Unserialize_Reader_Arr();
        $prevChar = null;
        for ($i = 0; $i < strlen($str); $i++) {
            $char = $str[$i];
            $arr = $reader->read($char, $prevChar);
            if (!is_null($arr)) {
                return $arr;
            }
            $prevChar = $char;
        }
        throw new Exception('Error during unserialization');
    }
}
