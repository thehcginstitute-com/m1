<?php
/**
 * 2024-03-03 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Implement the `hcg_customer_is_new()` function` template": https://github.com/thehcginstitute-com/m1/issues/442
 * @used-by app/design/frontend/default/mobileshoppe/template/catalog/product/compare/sidebar.phtml
 * @used-by app/design/frontend/default/mobileshoppe/template/checkout/cart/sidebar.phtml
 * @used-by app/design/frontend/default/mobileshoppe/template/checkout/onepage/payment.phtml
 * @used-by app/design/frontend/default/mobileshoppe/template/customer/account/navigation.phtml
 */
function hcg_customer_is_new():bool {return 4 === df_customer_group_id();}

/**
 * 2024-03-03 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Implement the `hcg_is_patient()` function` template": https://github.com/thehcginstitute-com/m1/issues/443
 * @used-by app/design/frontend/default/mobileshoppe/template/catalog/category/view.phtml
 * @used-by app/design/frontend/default/mobileshoppe/template/customer/account/dashboard/hello.phtml
 */
function hcg_is_patient():bool {return 1 === df_customer_group_id();}