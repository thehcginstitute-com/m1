<?php
use Mage_Admin_Model_Session as S;
/**
 * 2016-12-23
 * 2024-03-23 "Port `df_backend_session()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/531
 * @used-by Ebizmarts_MailChimp_Adminhtml_MailchimperrorsController::_isAllowed() (https://github.com/thehcginstitute-com/m1/issues/530)
 */
function df_backend_session():S {return Mage::getSingleton('admin/session');}