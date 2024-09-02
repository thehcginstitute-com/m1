<?php
use ReflectionClass as RC;

/**
 * 2016-05-06
 * By analogy with https://github.com/magento/magento2/blob/135f967/lib/internal/Magento/Framework/ObjectManager/TMap.php#L97-L99
 * 2016-05-23
 * Намеренно не объединяем строки в единное выражение, чтобы собака @ не подавляла сбои первой строки.
 * Такие сбои могут произойти при синтаксических ошибках в проверяемом классе
 * (похоже, getInstanceType как-то загружает код класса).
 * @used-by df_assert_class_exists()
 * @used-by df_block_output()
 * @used-by df_catalog_locator_exists()
 * @used-by df_con_hier_suf()
 */
function df_class_exists(string $c):bool {return @class_exists($c);}