<?php
/**
 * MailChimp For Magento
 *
 * @category  Ebizmarts_MailChimp
 * @author    Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date:     4/29/16 3:55 PM
 * @file:     Config.php
 */
class Ebizmarts_MailChimp_Model_Config
{
    const GENERAL_ACTIVE                        = 'mailchimp/general/active';
    const GENERAL_APIKEY                        = 'mailchimp/general/apikey';
    const GENERAL_OAUTH_WIZARD                  = 'mailchimp/general/oauth_wizard';
    const GENERAL_ACCOUNT_DETAILS               = 'mailchimp/general/account_details';
    const GENERAL_LIST                          = 'mailchimp/general/list';
    const GENERAL_OLD_LIST                      = 'mailchimp/general/old_list';
    const GENERAL_LIST_CHANGED_SCOPES           = 'mailchimp/general/list_changed_scopes';
    const GENERAL_CHECKOUT_SUBSCRIBE            = 'mailchimp/general/checkout_subscribe';
	/**
	 * 2024-04-22 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Helper_Data::getMCStoreId()`": https://github.com/thehcginstitute-com/m1/issues/574
	 * @used-by hcg_mc_sid()
	 */
    const GENERAL_MCSTOREID                     = 'mailchimp/general/storeid';
    const GENERAL_MCISSYNCING                   = 'mailchimp/general/is_syicing';
    const GENERAL_SUBMINSYNCDATEFLAG            = 'mailchimp/general/subminsyncdateflag';
    const GENERAL_TWO_WAY_SYNC                  = 'mailchimp/general/webhook_active';
    const GENERAL_UNSUBSCRIBE                   = 'mailchimp/general/webhook_delete';
    const GENERAL_WEBHOOK_ID                    = 'mailchimp/general/webhook_id';
    const GENERAL_LOG                           = 'mailchimp/general/enable_log';
    const GENERAL_ORDER_GRID                    = 'mailchimp/general/order_grid';
    const GENERAL_CUSTOM_MAP_FIELDS             = 'mailchimp/general/customer_map_fields';
    const GENERAL_MIGRATE_FROM_115              = 'mailchimp/general/migrate_from_115';
    const GENERAL_MIGRATE_FROM_116              = 'mailchimp/general/migrate_from_116';
    const GENERAL_MIGRATE_FROM_1164             = 'mailchimp/general/migrate_from_1164';
    const GENERAL_MIGRATE_FROM_1120             = 'mailchimp/general/migrate_from_1120';
    const GENERAL_MIGRATE_LAST_ORDER_ID         = 'mailchimp/general/migrate_last_order_id';
    const GENERAL_SUBSCRIBER_AMOUNT             = 'mailchimp/general/subscriber_batch_amount';
    const GENERAL_TIME_OUT                      = 'mailchimp/general/connection_timeout';
    const GENERAL_INTEREST_CATEGORIES           = 'mailchimp/general/interest_categories';
    const GENERAL_INTEREST_SUCCESS_BEFORE       = 'mailchimp/general/interest_success_before';
    const GENERAL_INTEREST_SUCCESS_AFTER        = 'mailchimp/general/interest_success_after';
    const GENERAL_INTEREST_SUCCESS_ACTIVE       = 'mailchimp/general/interest_in_success';
    const GENERAL_MAGENTO_MAIL                  = 'mailchimp/general/magento_mail';

    const ECOMMERCE_ACTIVE              = 'mailchimp/ecommerce/active';
    const ECOMMERCE_CUSTOMERS_OPTIN     = 'mailchimp/ecommerce/customers_optin';
    const ECOMMERCE_FIRSTDATE           = 'mailchimp/ecommerce/firstdate';
    const ECOMMERCE_MC_JS_URL           = 'mailchimp/ecommerce/mc_js_url';
    const ECOMMERCE_CUSTOMER_LAST_ID    = 'mailchimp/ecommerce/customer_last_id';
    const ECOMMERCE_PRODUCT_LAST_ID     = 'mailchimp/ecommerce/product_last_id';
    const ECOMMERCE_ORDER_LAST_ID       = 'mailchimp/ecommerce/order_last_id';
    const ECOMMERCE_CART_LAST_ID        = 'mailchimp/ecommerce/cart_last_id';
    const ECOMMERCE_PCD_LAST_ID         = 'mailchimp/ecommerce/pcd_last_id';
    const ECOMMERCE_RESEND_ENABLED      = 'mailchimp/ecommerce/resend_enabled';
    const ECOMMERCE_RESEND_TURN         = 'mailchimp/ecommerce/resend_turn';
    const ECOMMERCE_CUSTOMER_AMOUNT     = 'mailchimp/ecommerce/customer_batch_amount';
    const ECOMMERCE_PRODUCT_AMOUNT      = 'mailchimp/ecommerce/product_batch_amount';
    const ECOMMERCE_ORDER_AMOUNT        = 'mailchimp/ecommerce/order_batch_amount';
    const ECOMMERCE_IMAGE_SIZE          = 'mailchimp/ecommerce/image_size';
    const ECOMMERCE_SYNC_DATE           = 'mailchimp/ecommerce/sync_date';
    const ECOMMERCE_SEND_PROMO          = 'mailchimp/ecommerce/send_promo';
    const ECOMMERCE_XML_INCLUDE_TAXES   = 'mailchimp/ecommerce/include_taxes';

    const IMAGE_SIZE_DEFAULT            = 'image';
    const IMAGE_SIZE_SMALL              = 'small_image';
    const IMAGE_SIZE_THUMBNAIL          = 'thumbnail';
    const PRODUCT_IMAGE_CACHE_FLUSH     = 'mailchimp/ecommerce/product_image_cache_flush';
    const ADD_MAILCHIMP_LOGO_TO_GRID    = 1;
    const ADD_SYNC_STATUS_TO_GRID       = 2;
    const ADD_BOTH_TO_GRID              = 3;

    const ENABLE_POPUP                  = 'mailchimp/emailcatcher/popup_general';
    const POPUP_HEADING                 = 'mailchimp/emailcatcher/popup_heading';
    const POPUP_TEXT                    = 'mailchimp/emailcatcher/popup_text';
    const POPUP_FNAME                   = 'mailchimp/emailcatcher/popup_fname';
    const POPUP_LNAME                   = 'mailchimp/emailcatcher/popup_lname';
    const POPUP_WIDTH                   = 'mailchimp/emailcatcher/popup_width';
    const POPUP_HEIGHT                  = 'mailchimp/emailcatcher/popup_height';
    const POPUP_SUBSCRIPTION            = 'mailchimp/emailcatcher/popup_subscription';
    const POPUP_CAN_CANCEL              = 'mailchimp/emailcatcher/popup_cancel';
    const POPUP_COOKIE_TIME             = 'mailchimp/emailcatcher/popup_cookie_time';
    const POPUP_INSIST                  = 'mailchimp/emailcatcher/popup_insist';

    const ABANDONEDCART_ACTIVE          = 'mailchimp/abandonedcart/active';
    const ABANDONEDCART_FIRSTDATE       = 'mailchimp/abandonedcart/firstdate';
    const ABANDONEDCART_PAGE            = 'mailchimp/abandonedcart/page';
    const ABANDONEDCART_AMOUNT          = 'mailchimp/abandonedcart/cart_batch_amount';

    const MANDRILL_APIKEY               = 'mandrill/general/apikey';
    const MANDRILL_ACTIVE               = 'mandrill/general/active';
    const MANDRILL_LOG                  = 'mandrill/general/enable_log';

    const IS_CUSTOMER                   = "CUS";
    const IS_PRODUCT                    = "PRO";
    const IS_ORDER                      = "ORD";
    const IS_QUOTE                      = "QUO";
    const IS_SUBSCRIBER                 = "SUB";
    const IS_PROMO_RULE                 = "PRL";
    const IS_PROMO_CODE                 = "PCD";
}
