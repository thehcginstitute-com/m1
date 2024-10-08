<?php

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Fooman_EmailAttachments_Helper_Data extends Mage_Core_Helper_Abstract
{

    const LOG_FILE_NAME='fooman_emailattachments.log';

    /**
     * render pdf and attach to email
     *
     * @param        $pdf
     * @param        $mailObj
     * @param string $name
     *
     * @return mixed
     */
    function addAttachment($pdf, $mailObj, $name = "order")
    {
        try {
            $this->debug('ADDING ATTACHMENT: ' . $name);
            $file = $pdf->render();
            $mailObj->getMail()->createAttachment(
                $file, 'application/pdf', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, $name . '.pdf'
            );
            $this->debug('FINISHED ADDING ATTACHMENT: ' . $name);
        } catch (Exception $e) {
            Mage::log('Caught error while attaching pdf:' . $e->getMessage());
        }
        return $mailObj;
    }

    /**
     * attach file to email
     * supported types: pdf
     *
     * @param        $file
     * @param        $mailObj
     *
     * @return mixed
     */
    function addFileAttachment($file, $mailObj)
    {
        try {
            $this->debug('ADDING ATTACHMENT: ' . $file);
            $filePath = Mage::getBaseDir('media') . DS . 'pdfs' . DS .$file;
            if (file_exists($filePath)) {
                $mailObj->getMail()->createAttachment(
                    file_get_contents($filePath), 'application/pdf', Zend_Mime::DISPOSITION_ATTACHMENT,
                    Zend_Mime::ENCODING_BASE64, basename($filePath)
                );
            }
            $this->debug('FINISHED ADDING ATTACHMENT: ' . $file);

        } catch (Exception $e) {
            Mage::log('Caught error while attaching pdf:' . $e->getMessage());
        }
        return $mailObj;
    }

    /**
     * attach agreements for store and attach as
     * txt or html to email
     *
     * @param $storeId
     * @param $mailObj
     *
     * @return mixed
     */
    function addAgreements($storeId, $mailObj)
    {
        $this->debug('ADDING AGREEMENTS');
        $agreements = Mage::getModel('checkout/agreement')->getCollection()
            ->addStoreFilter($storeId)
            ->addFieldToFilter('is_active', 1);
        if ($agreements) {
            foreach ($agreements as $agreement) {
                $agreement->load($agreement->getId());
                $this->debug($agreement->getName());
                $cmsHelper = Mage::helper('cms');
                if (Mage::helper('core')->isModuleEnabled('Fooman_PdfCustomiser')) {
                    $pdf = Mage::getModel('pdfcustomiser/agreement')->getPdf(array($storeId=> $agreement));
                    $this->addAttachment($pdf, $mailObj, urlencode($agreement->getName()));
                } else {
                    $processor = $cmsHelper->getPageTemplateProcessor();
                    $content = $processor->filter($agreement->getContent());
                    if ($agreement->getIsHtml()) {
                        $html = '<html><head><title>' . $agreement->getName() . '</title></head><body>'
                            . $content . '</body></html>';
                        $mailObj->getMail()->createAttachment(
                            $html, 'text/html', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64,
                            urlencode($agreement->getName()) . '.html'
                        );
                    } else {
                        $mailObj->getMail()->createAttachment(
                            Mage::helper('core')->escapeHtml($content), 'text/plain',
                            Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64,
                            urlencode($agreement->getName()) . '.txt'
                        );
                    }
                }
                $this->debug('Done ' . $agreement->getName());
            }
        }
        $this->debug('FINISHED ADDING AGREEMENTS');
        return $mailObj;
    }

    /**
     * if in debug mode send message to logs
     *
     * @param $msg
     */
    function debug($msg)
    {
        if ($this->isDebugMode()) {
            Mage::helper('foomancommon')->sendToFirebug($msg);
            Mage::log($msg, null, self::LOG_FILE_NAME);
        }
    }

    /**
     * are we debugging
     *
     * @return bool
     */
    function isDebugMode()
    {
        return false;
    }

    function addButton($block)
    {
        $block->addButton(
            'print', array(
                'label'     => Mage::helper('sales')->__('Print'),
                'class'     => 'save',
                'onclick'   => 'setLocation(\'' . $this->getPrintUrl($block) . '\')'
            )
        );
    }

    /**
     * return url to print single order from order > view
     *
     * @param void
     * @access protected
     *
     * @return string
     */
    protected function getPrintUrl($block)
    {
        return $block->getUrl(
            'emailattachments/admin_order/print',
            array('order_id' => $block->getOrder()->getId())
        );
    }

    function getEmails($configPath, $storeId)
    {
        $data = Mage::getStoreConfig($configPath, $storeId);
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }
}