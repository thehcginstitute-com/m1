<?php

/**
 * Class IWD_SettlementReport_Model_System_Config_Backend_Cron
 */
class IWD_SettlementReport_Model_System_Config_Backend_Cron extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH = 'crontab/jobs/iwd_settlementreport_email_report/schedule/cron_expr';

    /**
     * {@inheritdoc}
     */
    protected function _afterSave()
    {
        $enabled = $this->getData('groups/emailing/fields/enable/value');
        $time = $this->getData('groups/emailing/fields/start_time/value');

        $cronExpr = ($enabled) ? sprintf("%s %s * * *", intval($time[1]), intval($time[0])) : "";

        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExpr)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
        } catch (Exception $e) {
            Mage::throwException(
                Mage::helper('adminhtml')->__('Unable to save the cron expression.') . $e->getMessage()
            );
        }
    }
}
