<?php
namespace HCG\Backend;
# 2024-04-01 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Restrict the access to bank card numbers in the backend": https://github.com/thehcginstitute-com/m1/issues/541
final class User {
	/**
	 * 2024-04-01
	 * @used-by \INT\DisplayCvv\B::_prepareSpecificInformation() (https://github.com/thehcginstitute-com/m1/issues/541)
	 * @used-by \Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Main::_prepareForm() (https://github.com/thehcginstitute-com/m1/issues/541)
	 */
	const CAN_VIEW_BANK_CARDS = 'hcg__can_view_bank_card_numbers';
}