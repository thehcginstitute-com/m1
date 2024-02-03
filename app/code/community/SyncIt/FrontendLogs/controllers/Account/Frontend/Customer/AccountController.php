<?php
/**
 * SyncIt Group Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the SyncIt Group that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.syncitgroup.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to office@syncitgroup.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.syncitgroup.com/ for more information
 * or send an email to office@syncitgroup.com
 *
 * @category   SyncIt
 * @package    SyncIt_Frontend_Logs
 * @copyright  Copyright (c) 2015 SyncIt Group (http://www.syncitgroup.com/)
 * @license    http://www.syncitgroup.com/LICENSE-1.0.html
 */

/**
 * Frontend Logs extension
 *
 * @category   SyncIt
 * @package    SyncIt_Frontend_Logs
 * @author     SyncIt Group Dev Team <support@syncitgroup.com>
 */

require_once 'Mage/Customer/controllers/AccountController.php';

class SyncIt_FrontendLogs_Account_Frontend_Customer_AccountController extends Mage_Customer_AccountController {

    /**
     * Login post action
     */
    public function loginPostAction() {
        if ($this->_getSession()->isLoggedIn()) {
        	//Mage::log('isLoggedIn');
            $this->_redirect('*/*/');
            return;
        }
        $session = $this->_getSession();

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');

            // Frontend customer logs // log data //
            $customerEmail = $login['username'];
            $customer = Mage::getModel("customer/customer");
            $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
            $customer->loadByEmail($customerEmail);
            $customerID = $customer->getId();
            // end of frontend customer logs //

            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }

                    // Frontend customer logs // Login Success //
                    $LogData = array(
                        'action_name' => 'Login Success',
                        'customer_id' => $customerID,
                        'customer_email' => $customerEmail,
                        'message' => ''
                    );
                    Mage::getModel("frontendlogs/authentication")->writeLogs($LogData);
                    // end of frontend customer logs //

                } catch (Mage_Core_Exception $e) {
                	//Mage::log('login exception:' . $e->getMessage());
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);

                            // Frontend customer logs // Login Failed //
                            $LogData = array(
                                'action_name' => 'Login Failed',
                                'customer_id' => $customerID,
                                'customer_email' => $customerEmail,
                                'message' => 'Account not confirmed'
                            );
                            Mage::getModel("frontendlogs/authentication")->writeLogs($LogData);
                            // end of frontend customer logs //

                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();

                            // Frontend customer logs // Login Failed //
                            if ($customerID) {
                                $LogData = array(
                                    'action_name' => 'Login Failed',
                                    'customer_id' => $customerID,
                                    'customer_email' => $customerEmail,
                                    'message' => 'Invalid password'
                                );
                            } else {
                                $LogData = array(
                                    'action_name' => 'Login Failed',
                                    'customer_email' => $customerEmail,
                                    'message' => 'Invalid email'
                                );
                            }
                            Mage::getModel("frontendlogs/authentication")->writeLogs($LogData);
                            // end of frontend customer logs //
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $session->addError($message);
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                	//Mage::log('login exception:' . $e->getMessage());
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
            	//Mage::log('Login and password are required.');
                $session->addError($this->__('Login and password are required.'));

                // Frontend customer logs // Login Failed //
                $LogData = array(
                    'action_name' => 'Login Failed',
                    'message' => 'Login and password inputs are empty'
                );
                Mage::getModel("frontendlogs/authentication")->writeLogs($LogData);
                // end of frontend customer logs //
            }
        }
        $this->_loginPostRedirect();
    }

    /**
     * Customer logout action
     */
    public function logoutAction() {

        // Frontend customer logs // log data //
        $currentCustomer = Mage::getSingleton('customer/session')->getCustomerId();
        $customer = Mage::getSingleton('customer/customer')->load($currentCustomer);
        $customerID = $customer->getEntityId();
        $customerEmail = $customer->getEmail();
        // end of frontend customer logs //

        $session = $this->_getSession();
        $session->logout()->renewSession();

        if (Mage::getStoreConfigFlag(Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD)) {
            $session->setBeforeAuthUrl(Mage::getBaseUrl());
        } else {
            $session->setBeforeAuthUrl($this->_getRefererUrl());
        }

        // Frontend customer logs
        $LogData = array(
            'action_name' => 'Logout Success',
            'customer_id' => $customerID,
            'customer_email' => $customerEmail,
            'message' => ''
        );
        Mage::getModel("frontendlogs/authentication")->writeLogs($LogData);
        // end of frontend customer logs //

        $this->_redirect('*/*/logoutSuccess');
    }

    /**
     * Create customer account action
     */
    public function createPostAction()
    {
        /** @var $session Mage_Customer_Model_Session */
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if (!$this->getRequest()->isPost()) {
            $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));
            $this->_redirectError($errUrl);
            return;
        }

        $customer = $this->_getCustomer();

        try {
            $errors = $this->_getCustomerErrors($customer);

            if (empty($errors)) {
                $customer->cleanPasswordsValidationData();
                $customer->save();
                $this->_dispatchRegisterSuccess($customer);
                $this->_successProcessRegistration($customer);
                return;
            } else {
                $this->_addSessionError($errors);
            }
        } catch (Mage_Core_Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost());
            if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                $url = $this->_getUrl('customer/account/forgotpassword');
                $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                $session->setEscapeMessages(false);

                // Frontend customer logs // Registration failed
                $userEmail = $this->getRequest()->getParam('email');
                $LogData = array(
                    'action_name' => 'Registration Failed',
                    'customer_email' => $userEmail,
                    'message' => 'Duplicate email'
                );
                Mage::getModel("frontendlogs/authentication")->writeLogs($LogData);
                // end of frontend customer logs //

            } else {
                $message = $e->getMessage();
            }
            $session->addError($message);
        } catch (Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost())
                ->addException($e, $this->__('Cannot save the customer.'));

            // Frontend customer logs // Registration failed
            $userEmail = $this->getRequest()->getParam('email');
            $LogData = array(
                'action_name' => 'Registration Failed',
                'customer_email' => $userEmail,
                'message' => 'Cannot save the customer'
            );
            Mage::getModel("frontendlogs/authentication")->writeLogs($LogData);
            // end of frontend customer logs //
        }
        $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));
        $this->_redirectError($errUrl);
    }

    /**
     * Success Registration
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Customer_AccountController
     */
    protected function _successProcessRegistration(Mage_Customer_Model_Customer $customer)
    {
        $session = $this->_getSession();
        if ($customer->isConfirmationRequired()) {
            /** @var $app Mage_Core_Model_App */
            $app = $this->_getApp();
            /** @var $store  Mage_Core_Model_Store*/
            $store = $app->getStore();
            $customer->sendNewAccountEmail(
                'confirmation',
                $session->getBeforeAuthUrl(),
                $store->getId()
            );
            $customerHelper = $this->_getHelper('customer');
            $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.',
                $customerHelper->getEmailConfirmationUrl($customer->getEmail())));
            $url = $this->_getUrl('*/*/index', array('_secure' => true));

            // Frontend customer logs // Registration failed
            $userEmail = $this->getRequest()->getParam('email');
            $LogData = array(
                'action_name' => 'Registration Success',
                'customer_email' => $userEmail,
                'message' => 'Confirmation required'
            );
            Mage::getModel("frontendlogs/authentication")->writeLogs($LogData);
            // end of frontend customer logs //
        } else {
            $session->setCustomerAsLoggedIn($customer);
            $url = $this->_welcomeCustomer($customer);

            // Frontend customer logs // Registration failed
            $userEmail = $this->getRequest()->getParam('email');
            $LogData = array(
                'action_name' => 'Registration Success',
                'customer_email' => $userEmail,
                'message' => ''
            );
            Mage::getModel("frontendlogs/authentication")->writeLogs($LogData);
            // end of frontend customer logs //
        }
        $this->_redirectSuccess($url);
        return $this;
    }
}