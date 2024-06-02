<?php
/**
 * 2024-06-02
 * 1) "Implement `df_is_email()`": https://github.com/mage2pro/core/issues/398
 * 2.1) https://www.php.net/manual/filter.examples.validation.php
 * 2.2) https://stackoverflow.com/a/12026863
 * 3) "Port `df_is_email()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/628
 * @used-by df_subscriber()
 * @param mixed $v
 */
function df_is_email($v):bool {return !!filter_var($v, FILTER_VALIDATE_EMAIL);}