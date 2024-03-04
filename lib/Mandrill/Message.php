<?php

/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 8/14/14
 * Time   : 6:48 PM
 * File   : Message.php
 * Module : Ebizmarts_Mandrill
 */
class Mandrill_Message extends Mandrill_Mandrill
{
    protected $_attachments = array();
    protected $_bcc = array();
    protected $_bodyText = false;
    protected $_bodyHtml = false;
    protected $_subject = null;
    protected $_from = null;
    protected $_to = array();
    protected $_headers = array();
    protected $_fromName;
    protected $_tags = array();
    protected $_returnPath = null;

    /**
     * Flag: whether or not email has attachments
     *
     * @var boolean
     */
    public $hasAttachments = false;

    public function getMail()
    {
        return $this;
    }

    /**
     * Adds an existing attachment to the mail message
     *
     * @param  Zend_Mime_Part $attachment
     * @return Mandrill_Message Provides fluent interface
     */
    public function addAttachment(Zend_Mime_Part $attachment)
    {
        $this->_attachments[] = $attachment;
        $this->hasAttachments = true;

        return $this;
    }

    /**
     * @param $body
     * @param string $mimeType
     * @param string $disposition
     * @param string $encoding
     * @param null   $filename
     * @return Zend_Mime_Part
     */
    public function createAttachment(
        $body,
        $mimeType = Zend_Mime::TYPE_OCTETSTREAM,
        $disposition = Zend_Mime::DISPOSITION_ATTACHMENT,
        $encoding = Zend_Mime::ENCODING_BASE64,
        $filename = null
    ) {
        $mp = new Zend_Mime_Part($body);
        $mp->encoding = $encoding;
        $mp->type = $mimeType;
        $mp->disposition = $disposition;
        $mp->filename = $filename;

        $this->addAttachment($mp);

        return $mp;
    }

    public function log($m)
    {
        $storeId = Mage::app()->getStore()->getId();
        if (Mage::getStoreConfig(Ebizmarts_MailChimp_Model_Config::MANDRILL_LOG, $storeId)) {
            Mage::log($m, Zend_Log::INFO, 'Mandrill.log');
        }
    }

    public function getAttachments()
    {
        $_attachments = array();

        foreach ($this->_attachments as $attachment) {
            /**
             * @var Zend_Mime_Part $attachment
             */
            $_attachments[] = array(
                'type' => $attachment->type,
                'name' => $attachment->filename,
                'content' => $attachment->getContent()
            );
        }

        return $_attachments;
    }

    public function addBcc($bcc)
    {
        if (is_array($bcc)) {
            foreach ($bcc as $email) {
                $this->_bcc[] = array(
                    'email' => $email,
                    'type' => 'bcc'
                );
            }
        } else {
            $this->_bcc[] = array(
                'email' => $bcc,
                'type' => 'bcc'
            );
        }
    }

    public function getBcc()
    {
        return $this->_bcc;
    }

    public function addTo($emails, $names = '')
    {
        if (is_array($emails)) {
            $max = count($emails);

            for ($i = 0; $i < $max; $i++) {
                if (isset($names[$i])) {
                    $this->_to[] = array(
                        'email' => $emails[$i],
                        'name' => $names[$i],
                        'type' => 'to'
                    );
                } else {
                    $this->_to[] = array(
                        'email' => $emails[$i],
                        'name' => '',
                        'type' => 'to'
                    );
                }
            }
        } else {
            $this->_to[] = array(
                'email' => $emails,
                'name' => $names,
                'type' => 'to'
            );
        }
    }
    public function getReplyTo()
    {
        $emails = array();
        foreach ($this->_to as $item) {
            $emails[] = $item['email'];
        }
        return $emails;
    }
    public function setReturnPath($email)
    {
//        if ($this->_returnPath === null) {
            $email = $this->_filterEmail($email);
            $this->_returnPath = $email;
            $this->_headers['Reply-To'] = sprintf('%s', $email);
//        }
        return $this;
    }
    public function getReturnPath()
    {
        if (null !== $this->_returnPath) {
            return $this->_returnPath;
        }

        return $this->_from;
    }
    public function getTo()
    {
        return $this->_to;
    }

    public function getRecipients()
    {
        return $this->getTo();
    }

    public function setBodyHtml($html, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
    {
        $this->_bodyHtml = $html;
    }

    public function getBodyHtml()
    {
        return $this->_bodyHtml;
    }

    public function setBodyText($txt, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
    {
        $this->_bodyText = $txt;
    }

    public function getBodyText()
    {
        return $this->_bodyText;
    }

    public function setSubject($subject)
    {
        if ($this->_subject === null) {
            $subject = $this->_filterOther($subject);
            $this->_subject = $subject;
        }

        return $this;
    }

    public function getSubject()
    {
        return $this->_subject;
    }

    public function setFrom($email, $name = null)
    {

        $email = $this->_filterEmail($email);
        $this->_from = $email;
        $this->_fromName = $name;

        return $this;
    }

    public function getFrom()
    {
        return $this->_from;
    }

    protected function _filterEmail($email)
    {
        $rule = array("\r" => '',
            "\n" => '',
            "\t" => '',
            '"' => '',
            ',' => '',
            '<' => '',
            '>' => '',
        );

        return strtr($email, $rule);
    }

    /**
     * Filter of name data
     *
     * @param  string $name
     * @return string
     */
    protected function _filterName($name)
    {
        $rule = array("\r" => '',
            "\n" => '',
            "\t" => '',
            '"' => "'",
            '<' => '[',
            '>' => ']',
        );

        return trim(strtr($name, $rule));
    }

    protected function _filterOther($data)
    {
        $rule = array("\r" => '',
            "\n" => '',
            "\t" => '',
        );

        return strtr($data, $rule);
    }

    public function setReplyTo($email, $name = null)
    {
        $email = $this->_filterEmail($email);
        $name = $this->_filterName($name);
        $this->_headers['Reply-To'] = sprintf('%s <%s>', $name, $email);
        return $this;
    }

    public function addHeader($name, $value, $append = false)
    {
        $prohibit = array('to', 'cc', 'bcc', 'from', 'subject',
            'reply-to', 'return-path',
            'date', 'message-id',
        );
        if (in_array(strtolower($name), $prohibit)) {
            /**
             * @see Zend_Mail_Exception
             */
            // require_once 'Zend/Mail/Exception.php';
            throw new Zend_Mail_Exception('Cannot set standard header from addHeader()');
        }

        $this->_headers[$name] = $value;

        return $this;
    }

    public function getHeaders()
    {
        return $this->_headers;
    }
    public function setTags($tags)
    {
        $this->_tags = $tags;
    }

    public function send()
    {
        $email = array();

        $email['to'] = array_merge($this->getTo(),$this->getBcc());

        $email['subject'] = $this->_subject;

        if (isset($this->_fromName)) {
            $email['from_name'] = $this->_fromName;
        }

        $email['from_email'] = $this->_from;

        if ($headers = $this->getHeaders()) {
            $email['headers'] = $headers;
        }

        if ($att = $this->getAttachments()) {
            $email['attachments'] = $att;
        }

        if ($this->_bodyHtml) {
            $email['html'] = $this->_bodyHtml;
        }

        if ($this->_bodyText) {
            $email['text'] = $this->_bodyText;
        }
        if ($this->_tags) {
            $email['tags'] = $this->_tags;
        }
        try {
            $this->messages->send($email);
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }

        return true;
    }
}
