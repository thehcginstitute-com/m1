<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Paygate
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author     Sreeprakash.N. <sree@schogini.com>
 * @copyright  Copyright (c) 2008 Schogini Systems (http://schogini.in)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Payjunction_Model_Payjunction extends Mage_Payment_Model_Method_Cc
{
    const REQUEST_METHOD_CC     = 'CC';
    const REQUEST_METHOD_ECHECK = 'ECHECK';

    const REQUEST_TYPE_AUTH_CAPTURE = 'AUTH_CAPTURE';
    const REQUEST_TYPE_AUTH_ONLY    = 'AUTH_ONLY';
    const REQUEST_TYPE_CAPTURE_ONLY = 'CAPTURE_ONLY';
    const REQUEST_TYPE_CREDIT       = 'CREDIT';
    const REQUEST_TYPE_VOID         = 'VOID';
    const REQUEST_TYPE_PRIOR_AUTH_CAPTURE = 'PRIOR_AUTH_CAPTURE';

    const ECHECK_ACCT_TYPE_CHECKING = 'CHECKING';
    const ECHECK_ACCT_TYPE_BUSINESS = 'BUSINESSCHECKING';
    const ECHECK_ACCT_TYPE_SAVINGS  = 'SAVINGS';

    const ECHECK_TRANS_TYPE_CCD = 'CCD';
    const ECHECK_TRANS_TYPE_PPD = 'PPD';
    const ECHECK_TRANS_TYPE_TEL = 'TEL';
    const ECHECK_TRANS_TYPE_WEB = 'WEB';

    const RESPONSE_DELIM_CHAR = ',';

    const RESPONSE_CODE_APPROVED = 1;
    const RESPONSE_CODE_DECLINED = 2;
    const RESPONSE_CODE_ERROR    = 3;
    const RESPONSE_CODE_HELD     = 4;

    protected $_code  = 'payjunction';

    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc 				= false;

    /**
     * Send authorize request to gateway
     *
     * @param   Varien_Object $payment
     * @param   decimal $amount
     * @return  Mage_Payjunction_Model_Payjunction
     */
    public function authorize(Varien_Object $payment, $amount)
    {
	    $error = false;
		$this->logit('authorize start', array());
		$this->logit('authorize start', get_class_methods(get_class($payment)));
		$this->logit('authorize get_class_vars', get_class_vars(get_class($payment)));
        if($amount>0){
            $payment->setAnetTransType(self::REQUEST_TYPE_AUTH_ONLY);
            $payment->setAmount($amount);

			$this->logit('Calling _buildRequest', array());
            $request = $this->_buildRequest($payment);
			$this->logit('buildrequest call returned', $request);

            $result  = $this->_postRequest($request);
			$this->logit('postRequest call returned', $result);
			
            $payment->setCcApproval($result->getApprovalCode())
                ->setLastTransId($result->getTransactionId())
                ->setCcTransId($result->getTransactionId())
                ->setCcAvsStatus($result->getAvsResultCode())
                ->setCcCidStatus($result->getCardCodeResponseCode());

			$code = $result->getResponseReasonCode();
            $text = $result->getResponseReasonText();

				
            switch ($result->getResponseCode()) {
                case self::RESPONSE_CODE_APPROVED:
                    $payment->setStatus(self::STATUS_APPROVED);
					if( !$order = $payment->getOrder() )
					{
						$order = $payment->getQuote();
					}
					$order->addStatusToHistory(
						$order->getStatus(),
						urldecode($result->getResponseReasonText()) . ' at PayJunction',
						$result->getResponseReasonText() . ' from PayJunction'
					);					
                    break;
                    
                case self::RESPONSE_CODE_DECLINED:
                    $error = Mage::helper('paygate')->__('Payment authorization transaction has been declined. ' . "$code - $text");
                    break;
                    
                default:
                    $error = Mage::helper('paygate')->__('Payment authorization error. ' . "$code - $text");
                    break;
                    
            }
            
        } else {
            $error = Mage::helper('paygate')->__('Invalid amount for authorization.');
            
        }

        if ($error !== false) {
            Mage::throwException($error);
        }
        
        return $this;

    }

    public function capture(Varien_Object $payment, $amount)
    {
		$this->logit('capture amount', $amount);
		$error = false;
		
		if ($payment->getCcTransId()) {
			$this->logit('capture cc transid DO ONLY CAPTURE FOR THIS TRANSID', $payment->getCcTransId());
			$payment->setAnetTransType(self::REQUEST_TYPE_PRIOR_AUTH_CAPTURE); // Sree do only capture
		} else {
			$payment->setAnetTransType(self::REQUEST_TYPE_AUTH_CAPTURE);    // Sree do full SALE
			$this->logit('capture NO cc transid SALE ', '');
		}
		
		$payment->setAmount($amount);
		
		$request = $this->_buildRequest($payment);
		$result  = $this->_postRequest($request);
		if ($result->getResponseCode() == self::RESPONSE_CODE_APPROVED) {
			$payment->setStatus(self::STATUS_APPROVED);
			$payment->setCcTransId($result->getTransactionId());
			$payment->setLastTransId($result->getTransactionId());
			if (!$order = $payment->getOrder()) {
				$order = $payment->getQuote();
			}
			$order->addStatusToHistory(
				$order->getStatus(),
				urldecode($result->getResponseReasonText()) . ' at PayJunction',
				$result->getResponseReasonText() . ' from PayJunction'
			);
		} else {
			if ($result->getResponseReasonText()) {
				$error = $result->getResponseReasonText();
			} else {
				$error = Mage::helper('paygate')->__('Error in capturing the payment');
			}
			if (!$order = $payment->getOrder()) {
				$order = $payment->getQuote();
			}
			$order->addStatusToHistory(
				$order->getStatus(),
				urldecode($error) . ' at PayJunction',
				$error . ' from PayJunction'
			);			
		}

		if ($error !== false) {
			Mage::throwException($error);
		}

		return $this;
    }

    /**
     * Check refund availability
     *
     * @return bool
     */
    public function canVoid(Varien_Object $payment)
    {
		return $this->_canVoid;
    }
	public function void(Varien_Object $payment)
	{
		$error = false;
		if ($payment->getVoidTransactionId() && $payment->getAmount() > 0) {
			$payment->setAnetTransType(self::REQUEST_TYPE_VOID);
			$request = $this->_buildRequest($payment);
			$request->setXTransId($payment->getVoidTransactionId());
			
			$result = $this->_postRequest($request);
			if ($result->getResponseCode()==self::RESPONSE_CODE_APPROVED) {
				$payment->setStatus(self::STATUS_VOID);
			} else {
				$errorMsg = $result->getResponseReasonText();
				$error = true;
			}

		} else {
			$errorMsg = Mage::helper('paygate')->__('Error in voiding the payment');
			$error = true;
		}
		
		if ($error !== false) {
			Mage::throwException($errorMsg);
		}
		return $this;	
	}

    /**
     * Check refund availability
     *
     * @return bool
     */
    public function canRefund()
    {
		return $this->_canRefund;
    }
	public function refund(Varien_Object $payment, $amount)
	{
		$error = false;
		//if ($payment->getRefundTransactionId() && $amount>0) {
		if ((($this->getConfigData('test') && $payment->getRefundTransactionId() == 0) || $payment->getRefundTransactionId()) && $amount>0) {
			$payment->setAnetTransType(self::REQUEST_TYPE_CREDIT);
			$request = $this->_buildRequest($payment);
			$request->setXTransId($payment->getRefundTransactionId());
			$request->setXAmount($amount);
			$result = $this->_postRequest($request);
			if ($result->getResponseCode()==self::RESPONSE_CODE_APPROVED) {
				$payment->setStatus(self::STATUS_SUCCESS);
				
				// For PayJunction a transaction id is generted for refund too
				// Store it for future possible use
				if (!$order = $payment->getOrder()) {
					$order = $payment->getQuote();
				}
				$order->addStatusToHistory(
					$order->getStatus(),
					'Refund Transaction ID: ' . $result->getTransactionId(),
					'Refund Transaction ID: ' . $result->getTransactionId()
				);
				
			} else {
				$errorMsg = $result->getResponseReasonText();
				$error = true;
			}

		} else {
			$errorMsg = Mage::helper('paygate')->__('Error in refunding the payment');
			$error = true;
		}

		if ($error !== false) {
			Mage::throwException($errorMsg);
		}
		return $this;
	}


    /**
     * Prepare request to gateway
     *
     * @link   http://www.authorize.net/support/AIM_guide.pdf
     * @param  Mage_Sales_Model_Document $order
     * @return unknown
     */
    protected function _buildRequest(Varien_Object $payment)
    {
	    $this->logit('Inside _buildRequest calling getOrder', array());
        $this->logit('Inside _buildRequest AAA', get_class($payment));
        $this->logit('Inside _buildRequest AAA', get_class_methods(get_class($payment)));

	    $order = $payment->getOrder();
	    $this->logit('Inside _buildRequest called getOrder', array());
        $this->logit('Inside _buildRequest BBB', get_class($order));
        $this->logit('Inside _buildRequest BBB', get_class_methods(get_class($order)));

        if (!$payment->getAnetTransMethod()) {
            $payment->setAnetTransMethod(self::REQUEST_METHOD_CC);
        }

       $this->logit('Inside _buildRequest A1', array());

        //$request = Mage::getModel('paygate/payjunction_request')
        $request = Mage::getModel('payjunction/payjunction_request')
            ->setXVersion(3.1)
            ->setXDelimData('True')
            ->setXDelimChar(self::RESPONSE_DELIM_CHAR)
            ->setXRelayResponse('False');

        $request->setXTestRequest($this->getConfigData('test') ? 'TRUE' : 'FALSE');    
		$request->setXLogin($this->getConfigData('login'))
            ->setXTranKey($this->getConfigData('trans_key'))
            ->setXType($payment->getAnetTransType())
            ->setXMethod($payment->getAnetTransMethod());

        if($payment->getAmount()){
            $request->setXAmount($payment->getAmount(),2);
            $request->setXCurrencyCode($order->getBaseCurrencyCode());
            
        }
        
        $this->logit('Inside _buildRequest A4', array());
        switch ($payment->getAnetTransType()) {
            case self::REQUEST_TYPE_CREDIT:
            case self::REQUEST_TYPE_VOID:
            case self::REQUEST_TYPE_PRIOR_AUTH_CAPTURE:
                $request->setXTransId($payment->getCcTransId());
                break;

            case self::REQUEST_TYPE_CAPTURE_ONLY:
                $request->setXAuthCode($payment->getCcAuthCode());
                break;
        }

        $this->logit('Inside _buildRequest A5', array());
        $this->logit('Inside _buildRequest A2', get_class($order));                   
        $this->logit('Inside _buildRequest A2', get_class_methods(get_class($order)));

        if (!empty($order)) {
            $request->setXInvoiceNum($order->getIncrementId());
            $billing = $order->getBillingAddress();
		    $this->logit('Inside _buildRequest CCC order->getBillingAddress', get_class($billing));
		    $this->logit('Inside _buildRequest CCC order->getBillingAddress', get_class_methods(get_class($billing)));
            if (!empty($billing)) {
				//Sree 17Nov2008
				$email = $billing->getEmail();
				if(!$email)$email = $order->getBillingAddress()->getEmail();
				if(!$email)$email = $order->getCustomerEmail();

                $request->setXFirstName($billing->getFirstname())
                    ->setXLastName($billing->getLastname())
                    ->setXCompany($billing->getCompany())
                    ->setXAddress($billing->getStreet(1))
                    ->setXCity($billing->getCity())
                    ->setXState($billing->getRegion())
                    ->setXZip($billing->getPostcode())
                    ->setXCountry($billing->getCountry())
                    ->setXPhone($billing->getTelephone())
                    ->setXFax($billing->getFax())
                    ->setXCustId($billing->getCustomerId())
                    ->setXCustomerIp($order->getRemoteIp())
                    ->setXCustomerTaxId($billing->getTaxId())
                    ->setXEmail($email)  //Sree 17Nov2008
                    ->setXEmailCustomer($this->getConfigData('email_customer'))
                    ->setXMerchantEmail($this->getConfigData('merchant_email'));
            }
			
            $shipping = $order->getShippingAddress();
			$this->logit('Inside _buildRequest DDD shipping = order->getShippingAddress()', get_class($shipping));
			if(!$shipping)$shipping = $billing;
            if (!empty($shipping)) {
                $request->setXShipToFirstName($shipping->getFirstname())
                    ->setXShipToLastName($shipping->getLastname())
                    ->setXShipToCompany($shipping->getCompany())
                    ->setXShipToAddress($shipping->getStreet(1))
                    ->setXShipToCity($shipping->getCity())
                    ->setXShipToState($shipping->getRegion())
                    ->setXShipToZip($shipping->getPostcode())
                    ->setXShipToCountry($shipping->getCountry());
            }

            $request->setXPoNum($payment->getPoNumber())
                ->setXTax($shipping->getTaxAmount())
                ->setXFreight($shipping->getShippingAmount());
        }

        $this->logit('Inside _buildRequest A6', array());
        switch ($payment->getAnetTransMethod()) {
            case self::REQUEST_METHOD_CC:
                if($payment->getCcNumber()){
                    $request->setXCardNum($payment->getCcNumber())
                        ->setXExpDate(sprintf('%02d-%04d', $payment->getCcExpMonth(), $payment->getCcExpYear()))
                        ->setXCardCode($payment->getCcCid());
                }
                break;

            case self::REQUEST_METHOD_ECHECK:
                $request->setXBankAbaCode($payment->getEcheckRoutingNumber())
                    ->setXBankName($payment->getEcheckBankName())
                    ->setXBankAcctNum($payment->getEcheckAccountNumber())
                    ->setXBankAcctType($payment->getEcheckAccountType())
                    ->setXBankAcctName($payment->getEcheckAccountName())
                    ->setXEcheckType($payment->getEcheckType());
                break;
        }
		
        $this->logit('Inside _buildRequest A7', array());
        return $request;
    }

    protected function _postRequest(Varien_Object $request)
    {
	    
	    $result = Mage::getModel('payjunction/payjunction_result');

		/**
		 * @TODO
		 * Sree handle exception
		 */
		$m = $request->getData();
	  	$this->logit("_postRequest m array", array('m' => $m));
		// Pre-Build Returned results
		$r = array (
		    0 => '1',
		    1 => '1',
		    2 => '1',
		    3 => '(TESTMODE) This transaction has been approved.',
		    4 => '000000',
		    5 => 'P',
		    6 => '0',
		    7 => '100000018',
		    8 => '',
		    9 => '2704.99',
		    10 => 'CC',
		    11 => 'auth_only',
		    12 => '',
		    13 => 'Sreeprakash',
		    14 => 'N.',
		    15 => 'Schogini',
		    16 => 'XYZ',
		    17 => 'City',
		    18 => 'Idaho',
		    19 => '695038',
		    20 => 'US',
		    21 => '1234567890',
		    22 => '',
		    23 => '',
		    24 => 'Sreeprakash',
		    25 => 'N.',
		    26 => 'Schogini',
		    27 => 'XYZ',
		    28 => 'City',
		    29 => 'Idaho',
		    30 => '695038',
		    31 => 'US',
		    32 => '',
		    33 => '',
		    34 => '',
		    35 => '',
		    36 => '',
		    37 => '382065EC3B4C2F5CDC424A730393D2DF',
		    38 => '',
		    39 => '',
		    40 => '',
		    41 => '',
		    42 => '',
		    43 => '',
		    44 => '',
		    45 => '',
		    46 => '',
		    47 => '',
		    48 => '',
		    49 => '',
		    50 => '',
		    51 => '',
		    52 => '',
		    53 => '',
		    54 => '',
		    55 => '',
		    56 => '',
		    57 => '',
		    58 => '',
		    59 => '',
		    60 => '',
		    61 => '',
		    62 => '',
		    63 => '',
		    64 => '',
		    65 => '',
		    66 => '',
		    67 => '',
		  );

    	//Replace the values from Magento 
	    $r[7]  = $m['x_invoice_num']; //InvoiceNumber
	    $r[8]  = ''; //Description
	    $r[9]  = $m['x_amount']; //Amount
	    $r[10] = $m['x_method']; //Method = CC
	    $r[11] = $m['x_type']; //TransactionType
	    $r[12] = $m['x_cust_id']; //CustomerId
	    $r[13] = $m['x_first_name']; 
	    $r[14] = $m['x_last_name'];
	    $r[15] = $m['x_company'];
	    $r[16] = $m['x_address'];
	    $r[17] = $m['x_city'];
	    $r[18] = $m['x_state'];
	    $r[19] = $m['x_zip'];
	    $r[20] = $m['x_country'];
	    $r[21] = $m['x_phone'];
	    $r[22] = $m['x_fax'];
	    $r[23] = '';
        //no shipping

        $m['x_ship_to_first_name'] = !isset($m['x_ship_to_first_name'])?$m['x_first_name']:$m['x_ship_to_first_name'];
		$m['x_ship_to_first_name'] = !isset($m['x_ship_to_first_name'])?$m['x_first_name']:$m['x_ship_to_first_name'];
		$m['x_ship_to_last_name'] = !isset($m['x_ship_to_last_name'])?$m['x_last_name']:$m['x_ship_to_last_name'];
		$m['x_ship_to_company'] = !isset($m['x_ship_to_company'])?$m['x_company']:$m['x_ship_to_company'];
		$m['x_ship_to_address'] = !isset($m['x_ship_to_address'])?$m['x_address']:$m['x_ship_to_address'];
		$m['x_ship_to_city'] = !isset($m['x_ship_to_city'])?$m['x_city']:$m['x_ship_to_city'];
		$m['x_ship_to_state'] = !isset($m['x_ship_to_state'])?$m['x_state']:$m['x_ship_to_state'];
		$m['x_ship_to_zip'] = !isset($m['x_ship_to_zip'])?$m['x_zip']:$m['x_ship_to_zip'];
		$m['x_ship_to_country'] = !isset($m['x_ship_to_country'])?$m['x_country']:$m['x_ship_to_country'];

	    $r[24] = $m['x_ship_to_first_name'];
	    $r[25] = $m['x_ship_to_last_name'];
	    $r[26] = $m['x_ship_to_company'];
	    $r[27] = $m['x_ship_to_address'];
	    $r[28] = $m['x_ship_to_city'];
	    $r[29] = $m['x_ship_to_state'];
	    $r[30] = $m['x_ship_to_zip'];
	    $r[31] = $m['x_ship_to_country'];

	    //Dummy Replace the values from PayJunction  
	    $r[0]  = '1';  // response_code
	    $r[1]  = '1';  // ResponseSubcode
	    $r[2]  = '1';  // ResponseReasonCode
	    $r[3]  = '(TESTMODE2) This transaction has been approved.'; //ResponseReasonText
	    $r[4]  = '000000'; //ApprovalCode
	    $r[5]  = 'P'; //AvsResultCode
	    $r[6]  = '0'; //TransactionId
	    $r[37] = '382065EC3B4C2F5CDC424A730393D2DF'; //Md5Hash
	    $r[39] = ''; //CardCodeResponse

		// Add PayJunction Here
		$this->logit("_payjunctionapi called", array('m' => $m));
        $rr = $this->_payjunctionapi($m);
  	    $this->logit("_payjunctionapi call returned back", array('rr' => $rr));

	    //Replace the values from PayJunction 
	    $r[0]  = $rr['response_code'];
	    $r[1]  = $rr['response_subcode'];
	    $r[2]  = $rr['response_reason_code'];
	    $r[3]  = $rr['response_reason_text']; //'(TESTMODE2) This transaction has been approved.'; //ResponseReasonText
	    $r[4]  = $rr['approval_code']; //'000000'; //ApprovalCode
	    $r[5]  = $rr['avs_result_code']; //'P'; //AvsResultCode
	    $r[6]  = $rr['transaction_id']; //'0'; //TransactionId
	    $r[37] = $rr['md5_hash'];
	    $r[39] = $rr['card_code_response'];
  	    $this->logit("after r array loaded with rr", array('r' => $r));

        if ($r) {
			$this->logit("setting", '');
            $result->setResponseCode( (int)str_replace('"','',$r[0]) );
            #$result->setResponseCode( 1 );
			$this->logit("setting 2", '');
            $result->setResponseSubcode((int)str_replace('"','',$r[1]));
			$this->logit("setting 3", '');
            $result->setResponseReasonCode((int)str_replace('"','',$r[2]));
			$this->logit("setting 4", '');
            $result->setResponseReasonText($r[3]);
			$this->logit("setting 5", '');
            $result->setApprovalCode($r[4]);
			$this->logit("setting 6", '');
            $result->setAvsResultCode($r[5]);
			$this->logit("setting 7", '');
            $result->setTransactionId($r[6]);
			$this->logit("setting 8", '');
            $result->setInvoiceNumber($r[7]);
			$this->logit("setting 9", '');
            $result->setDescription($r[8]);
			$this->logit("setting 10", '');
            $result->setAmount($r[9]);
			$this->logit("setting 11", '');
            $result->setMethod($r[10]);
			$this->logit("setting 12", '');
            $result->setTransactionType($r[11]);
			$this->logit("setting 13", '');
            $result->setCustomerId($r[12]);
			$this->logit("setting 14", '');
            $result->setMd5Hash($r[37]);
			$this->logit("setting 15", '');
            $result->setCardCodeResponseCode($r[39]);
			$this->logit("setting 16", '');
        } else {
             Mage::throwException(
                Mage::helper('paygate')->__('Error in payment gateway')
            );
        }
        return $result;
    }

	function sch_curl_get_contents($url,$time_out=30){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			#curl_setopt($ch, CURLOPT_HEADER, true); 
			#curl_setopt($ch, CURLOPT_GET, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                         
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			#curl_setopt($ch, CURLOPT_VERBOSE, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $time_out);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			//curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			return curl_exec($ch);
	}


	function _payjunctionapi($m)
	{
		$this->logit("_payjunctionapi inside", array());
		
		// get the login details
		$dc_logon 	 = $this->getConfigData('plogin');
		$dc_password = $this->getConfigData('password');
		$testmode	 = $this->getConfigData('test');
		$this->logit("_payjunctionapi inside", array());
		
		// check if its test mode
		if($testmode){
			$url="https://demo.payjunction.com/quick_link?dc_version=1.2&";
		}else{
			$url="https://payjunction.com/quick_link?dc_version=1.2&";
		}
		$this->logit("_payjunctionapi inside", array());
		
		// Add the common, mandatory details
		// $url =  "https://demo.payjunction.com/quick_link?dc_expiration_month=$expMonth&";
		$url .= "dc_logon={$dc_logon}&";
		$url .= "dc_password={$dc_password}&";
		$url .= "dc_name=" . urlencode($m['x_first_name']) . '+' . urlencode($m['x_last_name']) . '&';
		$url .= "dc_transaction_amount={$m['x_amount']}&";
		
		// What transaction type is it?
		$need_cc_details = 0;
		if ($m['x_type'] == 'AUTH_ONLY') { // Authorize only
			$url .= "dc_transaction_type=AUTHORIZATION&";
			$need_cc_details = 1;
			
		} else if ($m['x_type'] == 'AUTH_CAPTURE') { //Authorize and Capture
			$url .= "dc_transaction_type=AUTHORIZATION_CAPTURE&";
			$need_cc_details = 1;
			
		} elseif ($m['x_type'] == 'CAPTURE_ONLY' || $m['x_type'] == 'PRIOR_AUTH_CAPTURE') {  //Capture Only
			$url .= "dc_transaction_type=update&";
			$url .= "dc_posture=capture&";
			$url .= "dc_transaction_id={$m['x_trans_id']}&";
			$need_cc_details = 0;
			
		} elseif ($m['x_type'] == 'CREDIT') {  //Refund
			// unlike void and capture here we set the dc_transaction_type to CREDIT
			// and not "update" without dc_posture
			$url .= "dc_transaction_type=CREDIT&";
			$url .= "dc_transaction_id={$m['x_trans_id']}&";
			$need_cc_details = 0;
			
		} elseif ( $m['x_type'] == 'VOID' ) { //Void Only
			$url .= "dc_transaction_type=update&";
			$url .= "dc_posture=void&";
			$url .= "dc_transaction_id={$m['x_trans_id']}&";
			$need_cc_details = 0;
		}
		
		// Do we need to append the CC details
		if ($need_cc_details == 1) {
			// Credit card exp month and year
			$expMonth 	= substr($m['x_exp_date'],0,2);
			$expYear	= substr($m['x_exp_date'],-4);
			$exp_date	= $expMonth.$expYear;
				
			// Check AVS/CCV security needed
			$this->logit("check avs/ccv", array());
			$dc_security = '';
			if ($this->getConfigData('useavs') > 0) {
				$avstype = $this->getConfigData('avstype');
				if (empty($avstype)) {
					$avstype = 'A'; // Match address only by default
				}
				$dc_security = $avstype . '|';
			} else {
				$dc_security = '|';
			}
			if ($this->getConfigData('useccv') > 0) {
				$dc_security .= 'M|false';
				$url .= "dc_verification_number={$m['x_card_code']}&";
			} else {
				$dc_security .= '|false';
			}
			if ($this->getConfigData('useavs') == 2) {
				$dc_security .= '|false';
			} else {
				$dc_security .= '|true';
			}
			if ($this->getConfigData('useccv') == 2) {
				$dc_security .= '|false';
			} else {
				$dc_security .= '|true';
			}
			$this->logit("dc_security", array($dc_security));
			
			// This doesn't work in test mode
			if (!$testmode) {
				$url .= "dc_security={$dc_security}&";
			}
			
			// Add the credit card details to the URL
			$url .= "dc_number={$m['x_card_num']}&";
			$url .= "dc_expiration_month={$expMonth}&";
			$url .= "dc_expiration_year={$expYear}&";	
		}
		
		// billing address
		$url .= "dc_address=".urlencode(htmlentities(xmlentities($m['x_address']), ENT_QUOTES, 'UTF-8'))."&";
		$url .= "dc_city=".urlencode(htmlentities(xmlentities($m['x_city']), ENT_QUOTES, 'UTF-8'))."&";
		$url .= "dc_state={".htmlentities(xmlentities($m['x_state']), ENT_QUOTES, 'UTF-8')."}&";
		//$url .= "dc_zipcode={".htmlentities(xmlentities($m['x_zip']), ENT_QUOTES, 'UTF-8')."}&";
		$url .= "dc_zipcode={$m['x_zip']}&";
		$url .= "dc_country={".urlencode(htmlentities(xmlentities($m['x_country']), ENT_QUOTES, 'UTF-8'))."}&";
			
		$this->logit('URL', $url);

		// Get the results
		$txResult = $this->sch_curl_get_contents($url);
		$txResult = preg_replace("/\n/","",$txResult);
		$this->logit('RESULT', $txResult);
		
		// response is returned as a sequence of name/value pairs separated by the ASCII field separator (FS) character. 
		// Under ASCII (American Standard Code for Information Interchange), the 7-bit FS character has the following definition:
		// Character <FS>
		// Octal 034
		// Decimal 28
		// Hexadecimal 1C
		// For example:
		// This result transaction_id=25813response_code=00approval_code=PJ20APresponse_message=APPROVAL PJ20AP
		// when exploded will give this array
		// Array
		//	(
		//		[0] => transaction_id=25813
		//		[1] => response_code=00
		//		[2] => approval_code=PJ20AP
		//		[3] => response_message=APPROVAL PJ20AP
		//	)
		$temp1 = explode(chr(28), $txResult);
		foreach ($temp1 as $t) {
			$temp2 = explode('=', $t);
			if (strpos($temp2[0], 'dc_') !== FALSE && strpos($temp2[0], 'dc_') == 0) {
				$res[$temp2[0]] = $temp2[1];
			} else {
				$res['dc_' . $temp2[0]] = $temp2[1];
			}
		}		
		$this->logit('RESULT ARRAY', $res);

		/* Sample response from payjunction
			----Start Response Sent----
			dc_merchant_name=PayJunction - (demo)
			dc_merchant_address=123 Street Rd.
			dc_merchant_city=Santa Barbara
			dc_merchant_state=CA
			dc_merchant_zip=12345
			dc_merchant_phone=800-601-0230
			dc_device_id=1174
			dc_transaction_date=2008-12-08 11:01:02.186911
			dc_transaction_action=charge
			dc_approval_code=PJTEST
			dc_response_code=00
			dc_response_message=TEST APPROVAL
			dc_transaction_id=5456227
			dc_posture=capture
			dc_invoice_number=
			dc_notes=null
			dc_card_name=sreeprakash n.
			dc_card_brand=VSA
			dc_card_exp=XX/XX
			dc_card_number=XXXX-XXXX-XXXX-3344
			dc_card_address=
			dc_card_city=
			dc_card_zipcode=
			dc_card_state=
			dc_card_country=
			dc_base_amount=22.12
			dc_tax_amount=0.00
			dc_capture_amount=22.12
			dc_cashback_amount=0.00
			dc_shipping_amount=0.00
			----End Response Sent----
			
			Response for void / capture
			'dc_query_type=updatedc_query_status=truedc_posture=void'
			array (
			  'dc_query_type' => 'update',
			  'dc_query_status' => 'true',
			  'dc_posture' => 'void',
			)
			'dc_query_type=updatedc_query_status=truedc_posture=capture'
			array (
			  'dc_query_type' => 'update',
			  'dc_query_status' => 'true',
			  'dc_posture' => 'capture',
			)			
		*/
		
	    // Load Default Dummy Values
	    $rr 						= array();
	    $rr['response_code']		= '1';	
	    $rr['response_subcode']		= '1';
	    $rr['response_reason_code']	= '1';
	    $rr['response_reason_text'] = '(TESTMODE2) This transaction has been approved.';
	    $rr['approval_code'] 		= '000000'; //ApprovalCode
	    $rr['avs_result_code']		= 'P';
	    $rr['transaction_id']		= '0';
	    $rr['md5_hash']				= '382065EC3B4C2F5CDC424A730393D2DF';
	    $rr['card_code_response']	= '';
	    
	   	// Now check for approval
		if (isset($res['dc_query_type']) && $res['dc_query_type'] == 'update') {
			// this means its a capture or void transaction
			if ($res['dc_query_status'] == 'true') {
				//SUCCESS
				$this->logit('SUCCESS', '');
				$rr['response_code']		= '1';	
				$rr['response_subcode']		= '1';
				$rr['response_reason_code']	= '1';
				$rr['transaction_id']		= $m['x_trans_id'];
				
				if (isset($res['dc_response_message']) && !empty($res['dc_response_message'])) $rr['response_reason_text'] = $res['dc_response_message'];
				if (isset($res['dc_approval_code']) && !empty($res['dc_approval_code'])) $rr['approval_code'] = $res['dc_approval_code'];
				if (isset($res['dc_response_code']) && !empty($res['dc_response_code'])) $rr['avs_result_code'] = $res['dc_response_code'];
				if (isset($res['dc_response_code']) && !empty($res['dc_response_code'])) $rr['response_reason_code'] = $res['dc_response_code'];
				if ($rr['response_reason_text'] == '') {
					$rr['response_reason_text']  = ucwords($res['dc_posture']) . ' successful';
				}
				
			} else {
				//FAILED
				$this->logit('FAILED', '');	    
				$rr['response_code']		= '0';	
				$rr['response_subcode']		= '0';
				$rr['response_reason_code']	= '0';
				$rr['response_reason_text'] = '';
				$rr['approval_code'] 	= '000000'; //ApprovalCode
				$rr['avs_result_code']	= 'P';
				$rr['transaction_id']	= $m['x_trans_id'];
				if (isset($res['dc_response_code']) && !empty($res['dc_response_code'])) $rr['response_reason_code'] = $res['dc_response_code'];
				if (isset($res['dc_response_message']) && !empty($res['dc_response_message'])) $rr['response_reason_text'] = $res['dc_response_message'];
				if ($rr['response_reason_text'] == '') {
					$rr['response_reason_text']  = ucwords($res['dc_posture']) . ' failed';
				}
			}
			
		} else {
			if ((isset($res['dc_response_message']) && strpos($res['dc_response_message'], 'APPROV')!== false) &&  (isset($res['dc_response_code']) && ($res['dc_response_code'] == 0 || $res['dc_response_code'] == 85))) {
				//SUCCESS
				$this->logit('SUCCESS', '');
				$rr['response_code']		= '1';	
				$rr['response_subcode']		= '1';
				$rr['response_reason_code']	= '1';
				
				if (isset($res['dc_response_message']) && !empty($res['dc_response_message'])) $rr['response_reason_text'] = $res['dc_response_message'];
				if (isset($res['dc_approval_code']) && !empty($res['dc_approval_code'])) $rr['approval_code'] = $res['dc_approval_code'];
				if (isset($res['dc_response_code']) && !empty($res['dc_response_code'])) $rr['avs_result_code'] = $res['dc_response_code'];
				if (isset($res['dc_transaction_id']) && !empty($res['dc_transaction_id'])) $rr['transaction_id'] = $res['dc_transaction_id'];
				if (isset($res['dc_response_code']) && !empty($res['dc_response_code'])) $rr['response_reason_code'] = $res['dc_response_code'];
				
			} else {
				//FAILED
				$this->logit('FAILED', '');	    
				$rr['response_code']		= '0';	
				$rr['response_subcode']		= '0';
				$rr['response_reason_code']	= '0';
				$rr['response_reason_text'] = '';
				$rr['approval_code'] 	= '000000'; //ApprovalCode
				$rr['avs_result_code']	= 'P';
				$rr['transaction_id']	= '0';
				if (isset($res['dc_response_code']) && !empty($res['dc_response_code'])) $rr['response_reason_code'] = $res['dc_response_code'];
				if (isset($res['dc_response_message']) && !empty($res['dc_response_message'])) $rr['response_reason_text'] = $res['dc_response_message'];
			}
		}

		$this->logit('RESULT rr', $rr);
		//Mage::throwException('Sree');
		return $rr;
		
	}
	
	function logit($func, $arr=array())
	{

			if(!$this->getConfigData('debug')) return; // Set via Admin

			if(!isset($this->pth)||empty($this->pth)){
					$cfg = Mage::getConfig();
					$this->pth = $cfg->getBaseDir();
			}

			$f = $this->pth . '/magento_log.txt';

			if(!is_writable($f))return;

			$a='';
			if(count($arr)>0)$a=var_export($arr,true);
			@file_put_contents( $f , '----- Inside ' . $func . ' =1= ' . date('d/M/Y H:i:s') . ' -----' . "\n" . $a, FILE_APPEND);

	}
	

}

if( !function_exists( 'xmlentities' ) ) { 
	function xmlentities( $string ) { 
		$not_in_list = "A-Z0-9a-z\s_-"; 
		return preg_replace_callback( "/[^{$not_in_list}]/" , 'get_xml_entity_at_index_0' , $string ); 
	} 
	function get_xml_entity_at_index_0( $CHAR ) { 
		if( !is_string( $CHAR[0] ) || ( strlen( $CHAR[0] ) > 1 ) ) { 
			die( "function: 'get_xml_entity_at_index_0' requires data type: 'char' (single character). '{$CHAR[0]}' does not match this type." ); 
		} 
		switch( $CHAR[0] ) { 
			case "'":    case '"':    case '&':    case '<':    case '>': 
				return htmlspecialchars( $CHAR[0], ENT_QUOTES );    break; 
			default: 
				return numeric_entity_4_char($CHAR[0]);                break; 
		}        
	} 
	function numeric_entity_4_char( $char ) { 
		return "&#".str_pad(ord($char), 3, '0', STR_PAD_LEFT).";"; 
	}    
}