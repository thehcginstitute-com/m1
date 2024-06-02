<?php
/**
 * 2024-03-03
 * "Implement the `df_customer_is_anon()` function": https://github.com/thehcginstitute-com/m1/issues/447
 * @used-by app/design/frontend/default/mobileshoppe/template/catalog/category/view.phtml()
 */
function df_customer_is_anon():bool {return !df_customer_group_id();}