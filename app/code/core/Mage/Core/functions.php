<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Object destructor
 *
 * @param mixed $object
 */
function destruct($object)
{
    if (is_array($object)) {
        foreach ($object as $obj) {
            destruct($obj);
        }
    }
    unset($object);
}

/**
 * Translator function
 *
 * @return string
 * @deprecated 1.3
 */
function __()
{
    return Mage::app()->getTranslator()->translate(func_get_args());
}

/**
 * Tiny function to enhance functionality of ucwords
 * Will capitalize first letters and convert separators if needed
 * 2024-04-13 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Remove the unused 3rd argument of `uc_words()`": https://github.com/thehcginstitute-com/m1/issues/559
 * @param string $str
 * @param string $destSep
 * @return string
 */
function uc_words($str, $destSep = '_'):string {return str_replace(' ', $destSep, ucwords(
	/**
	 * 2024-04-13 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * 1) "Rework `uc_words()` to support PHP namespaces": https://github.com/thehcginstitute-com/m1/issues/558
	 * 2.1)  «If `search` is an array and `replace` is a string, then this replacement string is used for every value of `search`.»
	 * https://php.net/manual/function.str-replace.php
	 * 2.2) @see df_explode_multiple()
	 * 3) @see df_explode_class()
	 * 4) «Support PHP namespaces»: https://github.com/thehcginstitute-com/m1/issues/139
	 * 5) @see Varien_Autoload::autoload()
	 */
	str_replace(['\\', '_'], ' ', $str)
));}

/**
 * Simple sql format date
 *
 * @param bool $dayOnly
 * @return string
 * @deprecated use equivalent Varien method directly
 * @see Varien_Date::now()
 */
function now($dayOnly = false)
{
    return Varien_Date::now($dayOnly);
}

/**
 * Check whether sql date is empty
 *
 * @param string $date
 * @return bool
 */
function is_empty_date($date)
{
    return preg_replace('#[ 0:-]#', '', $date) === '';
}

/**
 * @used-by Mage_Core_Model_Layout::_getBlockInstance()
 * @used-by Mage_ProductAlert_Helper_Data::createBlock()
 * @param string $class
 * @return bool|string
 */
function mageFindClassFile($class)
{
    $classFile = uc_words($class, DIRECTORY_SEPARATOR) . '.php';
    $found = false;
    foreach (explode(PS, get_include_path()) as $path) {
        $fileName = $path . DS . $classFile;
        if (file_exists($fileName)) {
            $found = $fileName;
            break;
        }
    }
	# 2024-04-13 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# "Implement a diagnostics of errors in `mageFindClassFile()`": https://github.com/thehcginstitute-com/m1/issues/556
	if (!$found) {
		df_log("Unable to find the file «{$classFile} for the class «{$class}».", null, [
			'get_include_path' => explode(PS, get_include_path())
		]);
	}
    return $found;
}

/**
 * Custom error handler
 *
 * @param int $errno
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 * @return bool|void
 */
function mageCoreErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (strpos($errstr, 'DateTimeZone::__construct') !== false) {
        // there's no way to distinguish between caught system exceptions and warnings
        return false;
    }

    $errno = $errno & error_reporting();
    if ($errno == 0) {
        return false;
    }

    // PEAR specific message handling
    if (stripos($errfile . $errstr, 'pear') !== false) {
        // ignore strict and deprecated notices
        if (($errno == E_STRICT) || ($errno == E_DEPRECATED)) {
            return true;
        }
        // ignore attempts to read system files when open_basedir is set
        if ($errno == E_WARNING && stripos($errstr, 'open_basedir') !== false) {
            return true;
        }
    }

    $errorMessage = '';

    switch ($errno) {
        case E_ERROR:
            $errorMessage .= "Error";
            break;
        case E_WARNING:
            $errorMessage .= "Warning";
            break;
        case E_PARSE:
            $errorMessage .= "Parse Error";
            break;
        case E_NOTICE:
            $errorMessage .= "Notice";
            break;
        case E_CORE_ERROR:
            $errorMessage .= "Core Error";
            break;
        case E_CORE_WARNING:
            $errorMessage .= "Core Warning";
            break;
        case E_COMPILE_ERROR:
            $errorMessage .= "Compile Error";
            break;
        case E_COMPILE_WARNING:
            $errorMessage .= "Compile Warning";
            break;
        case E_USER_ERROR:
            $errorMessage .= "User Error";
            break;
        case E_USER_WARNING:
            $errorMessage .= "User Warning";
            break;
        case E_USER_NOTICE:
            $errorMessage .= "User Notice";
            break;
        case E_STRICT:
            $errorMessage .= "Strict Notice";
            break;
        case E_RECOVERABLE_ERROR:
            $errorMessage .= "Recoverable Error";
            break;
        case E_DEPRECATED:
            $errorMessage .= "Deprecated functionality";
            break;
        default:
            $errorMessage .= "Unknown error ($errno)";
            break;
    }

    $errorMessage .= ": {$errstr}  in {$errfile} on line {$errline}";
	/**
	 * 2024-02-19 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * 1) "PHP errors should be logged to separate files in the `var/log/mage2.pro` folder":
	 * https://github.com/thehcginstitute-com/m1/issues/390
	 * 2) @todo Call @see \Mage_Core_Model_App::setErrorHandler() in \Df\Core\Boot::init() instead:
	 * https://github.com/thehcginstitute-com/m1/issues/391
	 */
	df_log($errorMessage);
	# 2024-03-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# "PHP errors should be always converted to exceptions, regardless of the developer mode":
	# https://github.com/thehcginstitute-com/m1/issues/484
    throw new Exception($errorMessage);
}

/**
 * @param bool $return
 * @param bool $html
 * @param bool $showFirst
 * @return string|void
 *
 * @SuppressWarnings(PHPMD.ErrorControlOperator)
 */
function mageDebugBacktrace($return = false, $html = true, $showFirst = false)
{
    $d = debug_backtrace();
    $out = '';
    if ($html) {
        $out .= "<pre>";
    }
    foreach ($d as $i => $r) {
        if (!$showFirst && $i == 0) {
            continue;
        }
        // sometimes there is undefined index 'file'
        @$out .= "[$i] {$r['file']}:{$r['line']}\n";
    }
    if ($html) {
        $out .= "</pre>";
    }
    if ($return) {
        return $out;
    } else {
        echo $out;
    }
}

function mageSendErrorHeader()
{
    return;
}

function mageSendErrorFooter()
{
    return;
}

/**
 * @param string $path
 *
 * @SuppressWarnings(PHPMD.ErrorControlOperator)
 */
function mageDelTree($path)
{
    if (is_dir($path)) {
        $entries = scandir($path);
        foreach ($entries as $entry) {
            if ($entry != '.' && $entry != '..') {
                mageDelTree($path . DS . $entry);
            }
        }
        @rmdir($path);
    } else {
        @unlink($path);
    }
}

/**
 * @param string $string
 * @param string $delimiter
 * @param string $enclosure
 * @param string $escape
 * @return array
 */
function mageParseCsv($string, $delimiter = ",", $enclosure = '"', $escape = '\\')
{
    $elements = explode($delimiter, $string);
    for ($i = 0; $i < count($elements); $i++) {
        $nquotes = substr_count($elements[$i], $enclosure);
        if ($nquotes % 2 == 1) {
            for ($j = $i + 1; $j < count($elements); $j++) {
                if (substr_count($elements[$j], $enclosure) > 0) {
                    // Put the quoted string's pieces back together again
                    array_splice(
                        $elements,
                        $i,
                        $j - $i + 1,
                        implode($delimiter, array_slice($elements, $i, $j - $i + 1))
                    );
                    break;
                }
            }
        }
        if ($nquotes > 0) {
            // Remove first and last quotes, then merge pairs of quotes
            $qstr =& $elements[$i];
            $qstr = substr_replace($qstr, '', strpos($qstr, $enclosure), 1);
            $qstr = substr_replace($qstr, '', strrpos($qstr, $enclosure), 1);
            $qstr = str_replace($enclosure . $enclosure, $enclosure, $qstr);
        }
    }
    return $elements;
}

/**
 * @param string $dir
 * @return bool
 *
 * @SuppressWarnings(PHPMD.ErrorControlOperator)
 */
function is_dir_writeable($dir)
{
    if (is_dir($dir) && is_writable($dir)) {
        if (stripos(PHP_OS, 'win') === 0) {
            $dir    = ltrim($dir, DIRECTORY_SEPARATOR);
            $file   = $dir . DIRECTORY_SEPARATOR . uniqid(mt_rand()) . '.tmp';
            $exist  = file_exists($file);
            $fp     = @fopen($file, 'a');
            if ($fp === false) {
                return false;
            }
            fclose($fp);
            if (!$exist) {
                unlink($file);
            }
        }
        return true;
    }
    return false;
}
