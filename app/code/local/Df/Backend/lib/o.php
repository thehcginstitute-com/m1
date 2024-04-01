<?php
use Mage_Admin_Model_Session as S;
use Mage_Admin_Model_User as U;
/**
 * 2016-12-23
 * 2024-03-23 "Port `df_backend_session()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/531
 * @used-by Ebizmarts_MailChimp_Adminhtml_EcommerceController::_isAllowed() (https://github.com/thehcginstitute-com/m1/issues/530)
 * @used-by Ebizmarts_MailChimp_Adminhtml_MailchimperrorsController::_isAllowed() (https://github.com/thehcginstitute-com/m1/issues/530)
 * @used-by df_backend_user()
 */
function df_backend_session():S {return Mage::getSingleton('admin/session');}

/**
 * 2016-12-23
 * 2017-03-15 Если мы не в административной части, то df_backend_session()->getUser() просто вернёт null.
 * 2024-04-01 "Port `df_backend_user()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/542
 */
function df_backend_user():?U {return !df_is_backend() ? null: df_backend_session()->getUser();}