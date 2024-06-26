<?php

class Glew_Service_ModuleController extends Mage_Core_Controller_Front_Action
{
    protected $_helper = null;
    protected $_config = null;
    protected $_pageSize = null;
    protected $_pageNum = 0;
    protected $_startDate = null;
    protected $_endDate = null;
    protected $_sortDir = 'asc';
    protected $_filterField = 'created_at';
    protected $_id = null;

    protected function _construct()
    {
        $this->_helper = Mage::helper('glew');
        $this->_config = $this->_helper->getConfig();
        if ((bool) $pageSize = $this->getRequest()->getParam('page_size')) {
            $this->_pageSize = $pageSize;
        }
        if ((bool) $pageNum = $this->getRequest()->getParam('page_num')) {
            $this->_pageNum = $pageNum;
        }
        if ((bool) $startDate = $this->getRequest()->getParam('start_date')) {
            $this->_startDate = $startDate;
            if ((bool) $endDate = $this->getRequest()->getParam('end_date')) {
                $this->_endDate = $endDate;
            } else {
                $this->_endDate = date('Y-m-d');
            }
        } elseif ((bool) $updatedStartDate = $this->getRequest()->getParam('updated_start_date')) {
            $this->_filterField = 'updated_at';
            $this->_startDate = $updatedStartDate;
            if ((bool) $updatedEndDate = $this->getRequest()->getParam('updated_end_date')) {
                $this->_endDate = $updatedEndDate;
            } else {
                $this->_endDate = date('Y-m-d');
            }
        }
        if ((bool) $sortDir = $this->getRequest()->getParam('sort_dir')) {
            $this->_sortDir = $sortDir;
        }
        if ((bool) $id = $this->getRequest()->getParam('id')) {
            $this->_id = $id;
        }
    }

    function gotoglewAction()
    {
        $this->_redirectUrl('https://app.glew.io');
    }

    function abandoned_cartsAction()
    {
        try {
            $this->_initRequest();
            $collection = Mage::getModel('glew/types_abandonedCarts')->load(
                $this->_pageSize,
                $this->_pageNum,
                $this->_startDate,
                $this->_endDate,
                $this->_sortDir,
                $this->_filterField,
                $this->_id
            );
            $this->_sendResponse($collection);
        } catch (Exception $e) {
            if ($e->getCode() != 401) {
                print_r($e);
            }
            $this->_helper->logException($e, 'abandonedCarts');
        }
    }

    function customersAction()
    {
        try {
            $this->_initRequest();
            $collection = Mage::getModel('glew/types_customers')->load(
                $this->_pageSize,
                $this->_pageNum,
                $this->_startDate,
                $this->_endDate,
                $this->_sortDir,
                $this->_filterField,
                $this->_id
            );
            $this->_sendResponse($collection);
        } catch (Exception $e) {
            if ($e->getCode() != 401) {
                print_r($e);
            }
            $this->_helper->logException($e, 'customers');
        }
    }

    function ordersAction()
    {
        try {
            $this->_initRequest();
            $collection = Mage::getModel('glew/types_orders')->load(
                $this->_pageSize,
                $this->_pageNum,
                $this->_startDate,
                $this->_endDate,
                $this->_sortDir,
                $this->_filterField,
                $this->_id
            );
            $this->_sendResponse($collection);
        } catch (Exception $e) {
            if ($e->getCode() != 401) {
                print_r($e);
            }
            $this->_helper->logException($e, 'orders');
        }
    }

    function order_itemsAction()
    {
        try {
            $this->_initRequest();
            $collection = Mage::getModel('glew/types_orderItems')->load(
                $this->_pageSize,
                $this->_pageNum,
                $this->_startDate,
                $this->_endDate,
                $this->_sortDir,
                $this->_filterField,
                $this->_id
            );
            $this->_sendResponse($collection);
        } catch (Exception $e) {
            if ($e->getCode() != 401) {
                print_r($e);
            }
            $this->_helper->logException($e, 'orderItems');
        }
    }

    function storesAction()
    {
        try {
            $this->_initRequest();
            $collection = Mage::getModel('glew/types_stores')->load(
                $this->_pageSize,
                $this->_pageNum
            );
            $this->_sendResponse($collection);
        } catch (Exception $e) {
            if ($e->getCode() != 401) {
                print_r($e);
            }
            $this->_helper->logException($e, 'stores');
        }
    }

    function newsletter_subscribersAction()
    {
        try {
            $this->_initRequest();
            $collection = Mage::getModel('glew/types_subscribers')->load(
                $this->_pageSize,
                $this->_pageNum,
                $this->_sortDir,
                $this->_filterField,
                $this->_id
            );
            $this->_sendResponse($collection);
        } catch (Exception $e) {
            if ($e->getCode() != 401) {
                print_r($e);
            }
            $this->_helper->logException($e, 'subscribers');
        }
    }

    function productsAction()
    {
        try {
            $this->_initRequest();
            $collection = Mage::getModel('glew/types_products')->load(
                $this->_pageSize,
                $this->_pageNum,
                $this->_startDate,
                $this->_endDate,
                $this->_sortDir,
                $this->_filterField,
                $this->_id
            );
            $this->_sendResponse($collection);
        } catch (Exception $e) {
            if ($e->getCode() != 401) {
                print_r($e);
            }
            $this->_helper->logException($e, 'products');
        }
    }

    function product_alertsAction()
    {
        try {
            $this->_initRequest();
            $collection = Mage::getModel('glew/types_productAlerts')->load(
                $this->_pageSize,
                $this->_pageNum,
                $this->_startDate,
                $this->_endDate,
                $this->_sortDir,
                $this->_filterField,
                $this->_id
            );
            $this->_sendResponse($collection);
        } catch (Exception $e) {
            if ($e->getCode() != 401) {
                print_r($e);
            }
            $this->_helper->logException($e, 'productAlerts');
        }
    }

    function categoriesAction()
    {
        try {
            $this->_initRequest();
            $collection = Mage::getModel('glew/types_categories')->load(
                $this->_pageSize,
                $this->_pageNum,
                $this->_startDate,
                $this->_endDate,
                $this->_sortDir,
                $this->_filterField,
                $this->_id
            );
            $this->_sendResponse($collection);
        } catch (Exception $e) {
            if ($e->getCode() != 401) {
                print_r($e);
            }
            $this->_helper->logException($e, 'categories');
        }
    }

    function inventoryAction()
    {
        try {
            $this->_initRequest();
            $collection = Mage::getModel('glew/types_inventory')->load(
                $this->_pageSize,
                $this->_pageNum,
                $this->_sortDir,
                $this->_filterField,
                $this->_id
            );
            $this->_sendResponse($collection);
        } catch (Exception $e) {
            $this->_helper->logException($e, 'inventory');
        }
    }

    function versionAction()
    {
        try {
            $obj = new stdClass();
            $obj->glewPluginVersion = (string) Mage::getConfig()->getNode()->modules->Glew_Service->version;
            $obj->magentoVersion = (string) Mage::getVersion();
            $obj->phpVersion = (string) phpversion();
            $obj->moduleEnabled = $this->_config['enabled'];
            $obj->memoryLimit = @ini_get('memory_limit');
            $obj->maxExecutionTime = @ini_get('max_execution_time');
            $this->_sendResponse($obj);
        } catch (Exception $ex) {
            $this->_helper->logException($ex, 'version');
        }
    }

    function extensionsAction()
    {
        try {
            $this->_initRequest();
            $collection = Mage::getModel('glew/types_extensions')->load(
                $this->_pageSize,
                $this->_pageNum,
                $this->_sortDir,
                $this->_filterField
            );
            $this->_sendResponse($collection);
        } catch (Exception $e) {
            if ($e->getCode() != 401) {
                print_r($e);
            }
            $this->_helper->logException($e, 'extensions');
        }
    }

    function refund_itemsAction()
    {
        try {
            $this->_initRequest();
            $collection = Mage::getModel('glew/types_refundItems')->load(
                $this->_pageSize,
                $this->_pageNum,
                $this->_startDate,
                $this->_endDate,
                $this->_sortDir,
                $this->_filterField,
                $this->_id
            );
            $this->_sendResponse($collection);
        } catch (Exception $e) {
            if ($e->getCode() != 401) {
                print_r($e);
            }
            $this->_helper->logException($e, 'refund items');
        }
    }

    function refundsAction()
    {
        try {
            $this->_initRequest();
            $collection = Mage::getModel('glew/types_refunds')->load(
                $this->_pageSize,
                $this->_pageNum,
                $this->_startDate,
                $this->_endDate,
                $this->_sortDir,
                $this->_filterField,
                $this->_id
            );
            $this->_sendResponse($collection);
        } catch (Exception $e) {
            if ($e->getCode() != 401) {
                print_r($e);
            }
            $this->_helper->logException($e, 'refunds');
        }
    }

    function get_logAction()
    {
        try {
            $this->_initRequest();
            $logFile = $this->_helper->getLog();
            $lines = array();
            $fp = fopen($logFile, "r");
            if($fp)
            {
                while(!feof($fp))
                {
                    $line = fgets($fp, 4096);
                    array_push($lines, $line);
                    if(count($lines) > 1000) {
                        array_shift($lines);
                    }
                }
                fclose($fp);
                $this->getResponse()->setBody(implode("<br />", $lines));
            } else {
                $this->getResponse()->setBody('no log file');
            }
        } catch (Exception $e) {
            if ($e->getCode() != 401) {
                print_r($e);
            }
        }
    }

    protected function _sendResponse($items)
    {
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($items));
    }

    private function _initRequest()
    {
        if (!$this->_config['enabled']) {
            $this->_reject();

            return true;
        }
        $token = $this->_config['security_token'];
		# 2024-03-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# «Undefined index: X_GLEW_TOKEN in app/code/community/Glew/Service/controllers/ModuleController.php on line 384»
		# https://github.com/thehcginstitute-com/m1/issues/472
        if (!($authToken = dfa_try($_SERVER, 'HTTP_X_GLEW_TOKEN',  'X_GLEW_TOKEN'))) {
            $this->_reject();
        }
        if (trim($token) != trim($authToken)) {
            $this->_helper->log('Glew feed request with invalid security token: '.$authToken.' compared to stored token: '.$token);
            $this->_reject();
        }
    }

    private function _reject()
    {
        $this->getResponse()->setHttpResponseCode(401)->setBody('Invalid security token or module disabled');
        throw new Exception('Invalid security token or module disabled', 401);
    }
}
