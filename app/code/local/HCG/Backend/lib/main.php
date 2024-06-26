<?php
/**
 * 2024-04-01
 * @param int|string|null $id [optional]
 * @used-by Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Main::_prepareForm()
 * @used-by INT\DisplayCvv\B::_prepareSpecificInformation() (https://github.com/thehcginstitute-com/m1/issues/541)
 */
function hcg_is_super_admin($id = 0):bool {return in_array((int)$id ?: df_backend_user_id(), hcg_super_admins());}

/**
 * 2024-04-01
 * "Restrict the access to bank card numbers in the backend": https://github.com/thehcginstitute-com/m1/issues/541
 * @used-by hcg_is_super_admin()
 */
function hcg_super_admins():array {return [5, 51];}