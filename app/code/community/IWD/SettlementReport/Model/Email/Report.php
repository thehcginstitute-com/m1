<?php

/**
 * Class IWD_SettlementReport_Model_Email_Report
 */
class IWD_SettlementReport_Model_Email_Report extends Mage_Core_Model_Abstract
{
    const XML_PATH_EMAIL_FROM_EMAIL = 'iwd_settlementreport/emailing/from_email';
    const XML_PATH_EMAIL_TO_EMAIL = 'iwd_settlementreport/emailing/to_emails';
    const XML_PATH_EMAIL_TEMPLATE = 'iwd_settlementreport/emailing/transaction_email';
    const XML_PATH_EMAIL_FILES = 'iwd_settlementreport/emailing/files';

    /**
     * @param null $email
     */
    public function sendEmail($email = null)
    {
        $template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE);
        if (empty($template)) {
            Mage::throwException("Email template is empty. Please recheck extension settings");
        }

        $fromEmail = Mage::getStoreConfig(self::XML_PATH_EMAIL_FROM_EMAIL);
        if (empty($fromEmail)) {
            Mage::throwException("From email is empty. Please recheck extension settings");
        }

        $sender = array(
            'name' => "Settlement Report",
            'email' => $fromEmail
        );

        $recipientEmail = $this->getEmails($email);
        $recipientName = " ";

        $storeId = Mage::app()->getStore()->getId();

        $transactional = Mage::getModel('core/email_template');

        $transactional = $this->attachReports($transactional);

        $vars = array(
            'sender' => $sender,
            'date' => date('d-m-Y H:i')
        );

        $transactional->sendTransactional(
            $template,
            $sender,
            $recipientEmail,
            $recipientName,
            $vars,
            $storeId
        );
    }

    /**
     * @param $transactional
     * @return mixed
     */
    protected function attachReports($transactional)
    {
        $fileTypes = Mage::getStoreConfig(self::XML_PATH_EMAIL_FILES);
        $fileTypes = explode(',', $fileTypes);

        if (in_array('csv', $fileTypes)) {
            $file = $this->exportToCsvFile();
            $transactional = $this->attachFile($transactional, $file);
        }

        if (in_array('xml', $fileTypes)) {
            $file = $this->exportToExcelFile();
            $transactional = $this->attachFile($transactional, $file);
        }

        return $transactional;
    }

    /**
     * @param $transactional
     * @param $attachment
     * @return mixed
     */
    protected function attachFile($transactional, $attachment)
    {
        if (!empty($attachment) && file_exists($attachment)) {
            $transactional->getMail()
                ->createAttachment(
                    file_get_contents($attachment),
                    Zend_Mime::TYPE_OCTETSTREAM,
                    Zend_Mime::DISPOSITION_ATTACHMENT,
                    Zend_Mime::ENCODING_BASE64,
                    basename($attachment)
                );
        }

        return $transactional;
    }

    /**
     * @param $email
     * @return array|null
     */
    protected function getEmails($email)
    {
        if (empty($email)) {
            $email = Mage::getStoreConfig(self::XML_PATH_EMAIL_TO_EMAIL);
        }

        if (!empty($email) && strlen($email) > 5) {
            return explode(',', $email);
        }

        return null;
    }

    /**
     * @return string
     */
    public function exportToCsvFile()
    {
        $date = date('d-m-Y_H-i-s');
        $fileName = "transactions_{$date}.csv";

        $content = Mage::app()->getLayout()->createBlock('iwd_settlementreport/adminhtml_transactions_grid')
            ->setData('export_for_email', true)
            ->getCsvFile();

        return $this->saveFile($fileName, $content);
    }

    /**
     * @return string
     */
    public function exportToExcelFile()
    {
        $date = date('d-m-Y_H-i-s');
        $fileName = "transactions-{$date}.xml";

        $content = Mage::app()->getLayout()->createBlock('iwd_settlementreport/adminhtml_transactions_grid')
            ->setData('export_for_email', true)
            ->getExcelFile();

        return $this->saveFile($fileName, $content);
    }

    /**
     * @param $fileName
     * @param $content
     * @return string
     */
    public function saveFile($fileName, $content)
    {
        $io = new Varien_Io_File();

        $path = Mage::getBaseDir('var') . DS . 'export' . DS . 'iwd_settlement_reports' . DS;

        $file = null;
        if (is_array($content)) {
            if ($content['type'] == 'filename') {
                $file = $content['value'];
            }
        }

        if (!$file) {
            return "";
        }

        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($fileName, 'w+');
        $io->streamClose();

        $io->mv($file, $path . $fileName);

        return $path . $fileName;
    }
}
