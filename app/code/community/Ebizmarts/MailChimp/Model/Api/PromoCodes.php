<?php

/**
 * mailchimp-lib Magento Component
 *
 * @category  Ebizmarts
 * @package   mailchimp-lib
 * @author    Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ebizmarts_MailChimp_Model_Api_PromoCodes extends Ebizmarts_MailChimp_Model_Api_ItemSynchronizer
{
    const BATCH_LIMIT = 50;

    protected $_batchId;
    /**
     * @var Ebizmarts_MailChimp_Model_Api_PromoRules
     */
    protected $_apiPromoRules;

    /**
     * @var $_ecommercePromoCodesCollection Ebizmarts_MailChimp_Model_Resource_Ecommercesyncdata_PromoCodes_Collection
     */
    protected $_ecommercePromoCodesCollection;

    function __construct()
    {
        parent::__construct();
    }

    /**
     * @return array
     */
    function createBatchJson()
    {
        $mailchimpStoreId = $this->getMailchimpStoreId();
        $magentoStoreId = $this->getMagentoStoreId();

        $this->_ecommercePromoCodesCollection = $this->initializeEcommerceResourceCollection();
        $this->_ecommercePromoCodesCollection->setMailchimpStoreId($mailchimpStoreId);
        $this->_ecommercePromoCodesCollection->setStoreId($magentoStoreId);

        $batchArray = array();
        $this->_batchId = 'storeid-'
            . $magentoStoreId . '_'
            . Ebizmarts_MailChimp_Model_Config::IS_PROMO_CODE . '_'
            . $this->getDateHelper()->getDateMicrotime();
        $batchArray = array_merge($batchArray, $this->_getDeletedPromoCodes());
        $batchArray = array_merge($batchArray, $this->_getNewPromoCodes());
        $batchArray = array_merge($batchArray, $this->_getModifiedPromoCodes());

        return $batchArray;
    }

    /**
     * @return array
     */
    protected function _getDeletedPromoCodes()
    {
        $mailchimpStoreId = $this->getMailchimpStoreId();
        $batchArray = array();
        $deletedPromoCodes = $this->makeDeletedPromoCodesCollection();
        $counter = 0;

        foreach ($deletedPromoCodes as $promoCode) {
            $promoCodeId = $promoCode->getRelatedId();
            $promoRuleId = $promoCode->getDeletedRelatedId();
            $batchArray[$counter]['method'] = "DELETE";
            $batchArray[$counter]['path'] = '/ecommerce/stores/' . $mailchimpStoreId
                . '/promo-rules/' . $promoRuleId
                . '/promo-codes/' . $promoCodeId;
            $batchArray[$counter]['operation_id'] = $this->_batchId . '_' . $promoCodeId;
            $batchArray[$counter]['body'] = '';
            $this->deletePromoCodeSyncData($promoCodeId);
            $counter++;
        }

        return $batchArray;
    }

    /**
     * @return array
     */
    protected function _getNewPromoCodes()
    {
        $mailchimpStoreId = $this->getMailchimpStoreId();
        $magentoStoreId = $this->getMagentoStoreId();
        $batchArray = array();
        $helper = $this->getHelper();
        $dateHelper = $this->getDateHelper();
//        $newPromoCodes = $this->makePromoCodesCollection($magentoStoreId);
        // be sure that the orders are not in mailchimp
        $websiteId = Mage::getModel('core/store')->load($magentoStoreId)->getWebsiteId();
        $autoGeneratedCondition = "salesrule.use_auto_generation = 1 AND main_table.is_primary IS NULL";
        $notAutoGeneratedCondition = "salesrule.use_auto_generation = 0 AND main_table.is_primary = 1";

        $where = "m4m.mailchimp_sync_delta IS NULL AND website.website_id = " . $websiteId
            . " AND ( " . $autoGeneratedCondition . " OR " . $notAutoGeneratedCondition . ")";

        // send most recently created first
        $newPromoCodes = $this->buildEcommerceCollectionToSync(Ebizmarts_MailChimp_Model_Config::IS_PROMO_CODE, $where);
        $helper->logError((string) $newPromoCodes->getSelect());
        $counter = 0;

        foreach ($newPromoCodes as $promoCode) {
            $codeId = $promoCode->getCouponId();
            $ruleId = (int)$promoCode->getRuleId();

            try {
                $promoRuleSyncData = hcg_mc_syncd_get(
                    $ruleId,
                    Ebizmarts_MailChimp_Model_Config::IS_PROMO_RULE,
                    $mailchimpStoreId
                );

                if (!$promoRuleSyncData->getId()) {
                    $promoRuleMailchimpData = $this->getApiPromoRules()->getNewPromoRule(
                        $ruleId,
                        $mailchimpStoreId,
                        $magentoStoreId
                    );

                    if (!empty($promoRuleMailchimpData)) {
                        $batchArray[$counter] = $promoRuleMailchimpData;
                        $counter++;
                    } else {
                        $this->setCodeWithParentError($ruleId, $codeId);
                        continue;
                    }
                }

                if ($promoRuleSyncData['mailchimp_sync_error']) {
                    $this->setCodeWithParentError($ruleId, $codeId);
                    continue;
                }

                $promoCodeData = $this->generateCodeData($promoCode, $ruleId, $magentoStoreId);
                $promoCodeJson = json_encode($promoCodeData);

                if ($promoCodeJson !== false) {
                    if (!empty($promoCodeData)) {
                        $batchArray[$counter]['method'] = "POST";
                        $batchArray[$counter]['path'] = '/ecommerce/stores/' . $mailchimpStoreId
                            . '/promo-rules/' . $ruleId . '/promo-codes';
                        $batchArray[$counter]['operation_id'] = $this->_batchId . '_' . $codeId;
                        $batchArray[$counter]['body'] = $promoCodeJson;

                        $this->addSyncDataToken($codeId, $promoCode->getToken());
                        $counter++;
                    } else {
                        $error = $helper->__('Something went wrong when retrieving the information.');
                        $this->addSyncDataError(
                            $codeId,
                            $error,
                            null,
                            false,
                            $dateHelper->formatDate(null, "Y-m-d H:i:s")
                        );
                        continue;
                    }
                } else {
                    $jsonErrorMsg = json_last_error_msg();
                    $this->logSyncError(
                        "Promo code" . $codeId . " json encode failed (" . $jsonErrorMsg . ")",
                        Ebizmarts_MailChimp_Model_Config::IS_PROMO_CODE,
                        $magentoStoreId,
                        'magento_side_error',
                        'Json Encode Failure',
                        0,
                        $codeId,
                        0
                    );

                    $this->addSyncDataError(
                        $codeId,
                        $jsonErrorMsg,
                        null,
                        false,
                        $dateHelper->formatDate(null, "Y-m-d H:i:s")
                    );
                }
            } catch (Exception $e) {
                $this->logSyncError(
                    $e->getMessage(),
                    Ebizmarts_MailChimp_Model_Config::IS_PROMO_CODE,
                    $magentoStoreId,
                    'magento_side_error',
                    'Json Encode Failure',
                    0,
                    $codeId,
                    0
                );
            }
        }

        return $batchArray;
    }

    /**
     * @return array
     */
    protected function _getModifiedPromoCodes()
    {
        $mailchimpStoreId = $this->getMailchimpStoreId();
        $magentoStoreId = $this->getMagentoStoreId();
        $batchArray = array();
        $counter = 0;

        $modifiedPromoCodes = $this->buildEcommerceCollectionToSync(
            Ebizmarts_MailChimp_Model_Config::IS_PROMO_CODE,
            "m4m.mailchimp_sync_modified = 1",
            "modified"
        );

        foreach ($modifiedPromoCodes as $promoCode) {
            $promoCodeId = $promoCode->getRelatedId();
            $promoRuleId = $promoCode->getRuleId();

            $batchArray[$counter]['path'] = '/ecommerce/stores/' . $mailchimpStoreId
                . '/promo-rules/' . $promoRuleId
                . '/promo-codes/' . $promoCodeId;
            $batchArray[$counter]['operation_id'] = $this->_batchId . '_' . $promoCodeId;

            if ($promoCode->getMailchimpSyncModified()) {
                $batchArray[$counter]['method'] = "PATCH";
                $ruleData = $this->generateCodeData($promoCode, $promoRuleId, $magentoStoreId);
                $promoRuleJson = json_encode($ruleData);
                $batchArray[$counter]['body'] = $promoRuleJson;
                $counter++;
            }
        }

        return $batchArray;
    }

    /**
     * @return mixed
     */
    protected function getBatchLimitFromConfig()
    {
        $batchLimit = self::BATCH_LIMIT;
        return $batchLimit;
    }

    /**
     * @return Mage_SalesRule_Model_Resource_Coupon_Collection
     */
    protected function getItemResourceModelCollection()
    {
        return Mage::getResourceModel('salesrule/coupon_collection');
    }

    /**
     * @param $magentoStoreId
     * @return Mage_SalesRule_Model_Resource_Coupon_Collection
     */
    function makePromoCodesCollection($magentoStoreId)
    {
        $helper = $this->getHelper();
        /**
         * @var Mage_SalesRule_Model_Resource_Coupon_Collection $collection
         */
        $collection = $this->getItemResourceModelCollection();
        $helper->addResendFilter($collection, $magentoStoreId, Ebizmarts_MailChimp_Model_Config::IS_PROMO_CODE);

        $promoCollectionResource = $this->getEcommerceResourceCollection();
        $promoCollectionResource->addWebsiteColumn($collection);
        $promoCollectionResource->joinPromoRuleData($collection);

        return $collection;
    }

    /**
     * @return object
     */
    protected function makeDeletedPromoCodesCollection()
    {
        $deletedPromoCodes = hcg_mc_syncd_new()->getCollection();
        $where = "mailchimp_store_id = '" . $this->getMailchimpStoreId()
            . "' AND type = '" . Ebizmarts_MailChimp_Model_Config::IS_PROMO_CODE
            . "' AND mailchimp_sync_deleted = 1";

        $this->_ecommercePromoCodesCollection->addWhere($deletedPromoCodes, $where, $this->getBatchLimitFromConfig());

        return $deletedPromoCodes;
    }

    /**
     * @param $collection
     */
    function joinMailchimpSyncDataWithoutWhere($collection, $mailchimpStoreId=null)
    {
        $columns = array(
            "m4m.related_id",
            "m4m.type",
            "m4m.mailchimp_store_id",
            "m4m.mailchimp_sync_delta",
            "m4m.mailchimp_sync_modified"
        );

        $this->_ecommercePromoCodesCollection->joinLeftEcommerceSyncData($collection, $columns);
    }

    protected function generateCodeData($promoCode, $promoRuleId, $magentoStoreId)
    {
        $data = array();
        $data['id'] = $promoCode->getCouponId();
        $data['code'] = $promoCode->getCode();;
        $data['redemption_url'] = $this->getRedemptionUrl($promoCode, $magentoStoreId);
        $data['usage_count'] = (integer) $promoCode->getTimesUsed();
        $data['created_at_foreign'] = $promoCode->getCreatedAt();
        $data['updated_at_foreign'] = Mage::getSingleton('core/date')->date("Y-m-d H:i:s");

        $promoRule = $this->getPromoRule($promoRuleId);
        $data['enabled'] = (boolean)$promoRule->getIsActive();

      return $data;
    }

    protected function getRedemptionUrl($promoCode, $magentoStoreId)
    {
        $token = $this->getToken();
        $promoCode->setToken($token);
        $couponId = $promoCode->getCouponId();

        $url = Mage::getModel('core/url')->setStore($magentoStoreId)->getUrl(
            'mailchimp/cart/loadcoupon',
            array(
                    '_nosid' => true,
                    '_secure' => true,
                    'coupon_id' => $couponId,
                    'coupon_token' => $token
                )
        )
            . 'mailchimp/cart/loadcoupon?coupon_id='
            . $couponId
            . '&coupon_token='
            . $token;

        return $url;
    }

    /**
     * @return string
     */
    protected function getToken()
    {
        $token = hash('md5', rand(0, 9999999));

        return $token;
    }

    /**
     * @return Ebizmarts_MailChimp_Model_Api_PromoRules|false|Mage_Core_Model_Abstract
     */
    function getApiPromoRules()
    {
        if (!$this->_apiPromoRules) {
            $this->_apiPromoRules = Mage::getModel('mailchimp/api_promoRules');
        }

        return $this->_apiPromoRules;
    }

    /**
     * @param $codeId
     */
    function update($codeId)
    {
        $this->_setModified($codeId);
    }

    /**
     * @param $codeId
     */
    protected function _setModified($codeId)
    {
        $promoCodes = hcg_mc_syncd_new()->getAllEcommerceSyncDataItemsPerId(
            $codeId,
            Ebizmarts_MailChimp_Model_Config::IS_PROMO_CODE
        );
        /**
         * @var $promoCode Ebizmarts_MailChimp_Model_Api_PromoCodes
         */
        foreach ($promoCodes as $promoCode) {
            $mailchimpStoreId = $promoCode->getMailchimpStoreId();
            $this->setMailchimpStoreId($mailchimpStoreId);
            $this->markSyncDataAsModified($codeId);
        }
    }

    /**
     * @param $codeId
     * @param $promoRuleId
     */
    function markAsDeleted($codeId, $promoRuleId)
    {
        $this->_setDeleted($codeId, $promoRuleId);
    }

    /**
     * @param $codeId
     * @param $promoRuleId
     */
    protected function _setDeleted($codeId, $promoRuleId)
    {
        $promoCodes = hcg_mc_syncd_new()->getAllEcommerceSyncDataItemsPerId(
            $codeId,
            Ebizmarts_MailChimp_Model_Config::IS_PROMO_CODE
        );

        foreach ($promoCodes as $promoCode) {
            $mailchimpStoreId = $promoCode->getMailchimpStoreId();
            $this->setMailchimpStoreId($mailchimpStoreId);
            $this->addDeletedRelatedId($codeId, $promoRuleId);
        }
    }

    /**
     * @param $promoRule
     * @throws Exception
     */
    function deletePromoCodesSyncDataByRule($promoRule)
    {
        $promoCodeIds = $this->getPromoCodesForRule($promoRule->getRelatedId());

        foreach ($promoCodeIds as $promoCodeId) {
            $promoCodeSyncDataItems = hcg_mc_syncd_new()->getAllEcommerceSyncDataItemsPerId(
                $promoCodeId,
                Ebizmarts_MailChimp_Model_Config::IS_PROMO_CODE
            );

            foreach ($promoCodeSyncDataItems as $promoCodeSyncDataItem) {
                $promoCodeSyncDataItem->delete();
            }
        }
    }

    /**
     * @param $promoCodeId
     */
    function deletePromoCodeSyncData($promoCodeId)
    {
        $promoCodeSyncDataItem = hcg_mc_syncd_get(
            (int)$promoCodeId,
            Ebizmarts_MailChimp_Model_Config::IS_PROMO_CODE,
            $this->getMailchimpStoreId()
        );
        $promoCodeSyncDataItem->delete();
    }

    /**
     * @param $promoRuleId
     * @return array
     */
    protected function getPromoCodesForRule($promoRuleId)
    {
        $promoCodes = array();
        $helper = $this->getHelper();
        $promoRules = hcg_mc_syncd_new()->getAllEcommerceSyncDataItemsPerId(
            $promoRuleId,
            Ebizmarts_MailChimp_Model_Config::IS_PROMO_RULE
        );

        foreach ($promoRules as $promoRule) {
            $mailchimpStoreId = $promoRule->getMailchimpStoreId();
            $api = $helper->getApiByMailChimpStoreId($mailchimpStoreId);

            if ($api !== null) {
                try {
                    $mailChimpPromoCodes = $api->ecommerce->promoRules->promoCodes
                        ->getAll($mailchimpStoreId, $promoRuleId);

                    foreach ($mailChimpPromoCodes['promo_codes'] as $promoCode) {
                        $this->deletePromoCodeSyncData($promoCode['id']);
                    }
                } catch (MailChimp_Error $e) {
                    $this->logSyncError(
                        $e->getFriendlyMessage(),
                        Ebizmarts_MailChimp_Model_Config::IS_PROMO_RULE,
                        $this->getMagentoStoreId(),
                        'magento_side_error',
                        'Problem retrieving object',
                        0,
                        $promoRuleId,
                        0
                    );
                }
            }
        }

        return $promoCodes;
    }

    /**
     * @param $promoCodeId
     * @return string
     */
    protected function getPromoRuleIdByCouponId($promoCodeId)
    {
        $coupon = Mage::getModel('salesrule/coupon')->load($promoCodeId);
        return $coupon->getRuleId();
    }

    /**
     * @param $ruleId
     * @param $codeId
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function setCodeWithParentError($ruleId, $codeId)
    {
        $dateHelper = $this->getDateHelper();
        $error = Mage::helper('mailchimp')->__(
            'Parent rule with id ' . $ruleId . ' has not been correctly sent.'
        );
        $this->addSyncDataError(
            $codeId,
            $error,
            null,
            false,
            $dateHelper->formatDate(null, "Y-m-d H:i:s")
        );
    }

    /**
     * @return string
     */
    protected function getItemType()
    {
        return Ebizmarts_MailChimp_Model_Config::IS_PROMO_CODE;
    }

    /**
     * @return Ebizmarts_MailChimp_Model_Resource_Ecommercesyncdata_PromoCodes_Collection
     */
    function initializeEcommerceResourceCollection()
    {
        /**
         * @var $collection Ebizmarts_MailChimp_Model_Resource_Ecommercesyncdata_PromoCodes_Collection
         */
        $collection = Mage::getResourceModel('mailchimp/ecommercesyncdata_promoCodes_collection');

        return $collection;
    }

    /**
     * @return Ebizmarts_MailChimp_Model_Resource_Ecommercesyncdata_PromoCodes_Collection
     */
    function getEcommerceResourceCollection()
    {
        return $this->_ecommercePromoCodesCollection;
    }

    protected function addFilters($collection, $isNewItem = null)
    {
        $magentoStoreId = $this->getMagentoStoreId();
        $helper = $this->getHelper();
        /**
         * @var Mage_SalesRule_Model_Resource_Coupon_Collection $collection
         */
        $helper->addResendFilter($collection, $magentoStoreId, Ebizmarts_MailChimp_Model_Config::IS_PROMO_CODE);

        $promoCollectionResource = $this->getEcommerceResourceCollection();
        $promoCollectionResource->addWebsiteColumn($collection);
        $promoCollectionResource->joinPromoRuleData($collection);
    }

    /**
     * @param $ruleId
     * @return Mage_SalesRule_Model_Rule
     */
    protected function getPromoRule($ruleId)
    {
        return Mage::getModel('salesrule/rule')->load($ruleId);
    }
}
