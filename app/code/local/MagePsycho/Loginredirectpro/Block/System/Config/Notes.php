<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Loginredirectpro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Loginredirectpro_Block_System_Config_Notes extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $helper = Mage::helper('magepsycho_loginredirectpro');
        $notesText = "<div style='padding: 10px;'>
You can use either an relative url or absolute url for custom url redirection. Also you can use custom variables like {{referer}}, {{redirect_to}}, {{assigned_base_url}} etc. as redirection url.
<br /><br />
<strong>{$helper->__('Valid Examples:')}</strong><br />
- /<br />
- /welcome<br />
- /vendor/{{user_name}} <br />
- {{referer}}<br />
- {{assigned_base_url}}<br />
- {{redirect_to}}<br />
- http://my-another-store.com/welcome <br />
- http://mystore.com/read-me-first <br /><br />
<strong>{$helper->__('Notes:')}</strong>
<br /> 1. {$helper->__('/ denotes the base url of current store, used for relative url redirection.')}<br />
2. {$helper->__('{{referer}} variable is used when you want to redirect back to previous page.')} <br />
3. {$helper->__('{{assigned_base_url}} variable is used when you want to redirect customer to their assigned website.')} <br />
4. {$helper->__('{{redirect_to}} is used when you want to redirect to the url mentioned in the query string(Ref: Misc Settings > Redirect To Param)')} <br />
5. {$helper->__('You can also use absolute url for url redirection. Example: http://my-another-store.com/welcome')}<br />
6. {$helper->__('Other available variables are: {{ip}} - IP Address, {{country_code}} - Country Code, {{user_name}} - User Full Name, {{user_email}} - User Email Address, {{user_id}} - User Id, {{user_group_id}} - User Group Id')}<br />
7. {$helper->__('If Customer Group Wise Redirection Url is not defined then Default Redirection Url will be used.')}
</div>
";

        $notes = '<a href="javascript:void(0)" onclick="showNotesOnRedirection()">' . $helper->__('View Notes on Redirection Url') . '</a>'  . '
		<script>function showNotesOnRedirection() {
		win = new Window({className: "magento", title: "' . $helper->__('Notes on Redirection Url') . '", width:650, height:450, destroyOnClose: true, recenterAuto:false});
win.getContent().update("' . str_replace(array("\n","\r","\r\n"),'', $notesText) . '");
win.showCenter(); }
</script>
		';
        return $notes;
    }
}