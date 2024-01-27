<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Customerregfields
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Customerregfields_Helper_Config extends MagePsycho_Customerregfields_Helper_Data
{
    const XML_PATH_ENABLE_LOG                   = 'option/enable_log';
    const XML_PATH_DOMAIN_TYPE                  = 'option/domain_type';

    const XML_PATH_ALLOWED_CUSTOMER_GROUPS      = 'group/allowed_customer_groups';
    const XML_PATH_GROUP_IS_REQUIRED            = 'group/group_is_required';
    const XML_PATH_GROUP_SELECTION_EDITABLE     = 'group/group_selection_editable';
    const XML_PATH_GROUP_AVAILABLE_CHECKOUT     = 'group/group_selection_checkout';
    const XML_PATH_GROUP_SELECTION_LABEL        = 'group/group_selection_label';

    const XML_PATH_GROUP_SELECTION_TYPE         = 'group/customer_group_selection_type';

    const XML_PATH_GROUP_CODE_DATA              = 'group/groupcode_data';
    const XML_PATH_GROUP_CODE_ERROR_MESSAGE     = 'group/group_code_error_message';

    function isLogEnabled($storeId = null)
    {
        return $this->cfg(self::XML_PATH_ENABLE_LOG, $storeId);
    }

    function getGroupSelectionType($storeId = null)
    {
        return $this->cfg(self::XML_PATH_GROUP_SELECTION_TYPE, $storeId);
    }

    function getAllowedCustomerGroups($storeId = null)
    {
        return $this->cfg(self::XML_PATH_ALLOWED_CUSTOMER_GROUPS, $storeId);
    }

    function isGroupFieldRequired($storeId = null)
    {
        return $this->cfg(self::XML_PATH_GROUP_IS_REQUIRED, $storeId);
    }

    function isGroupSelectionEditable($storeId = null)
    {
        return $this->cfg(self::XML_PATH_GROUP_SELECTION_EDITABLE, $storeId);
    }

    function isEnabledForCheckout($storeId = null)
    {
        return $this->cfg(self::XML_PATH_GROUP_AVAILABLE_CHECKOUT, $storeId);
    }

    function getGroupSelectionLabel($storeId = null)
    {
        return $this->cfg(self::XML_PATH_GROUP_SELECTION_LABEL, $storeId);
    }

    function getGroupCodeData($storeId = null)
    {
        return $this->cfg(self::XML_PATH_GROUP_CODE_DATA, $storeId);
    }

    function getGroupCodeErrorMessage($storeId = null)
    {
        return $this->cfg(self::XML_PATH_GROUP_CODE_ERROR_MESSAGE, $storeId);
    }
}