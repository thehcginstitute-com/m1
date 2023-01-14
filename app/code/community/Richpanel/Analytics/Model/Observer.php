<?php
/**
 * Catch events and track them to Richpanel API
 *
 * @author Shubhanshu Chouhan <shubhanshu@richpanel.com>
 */
class Richpanel_Analytics_Model_Observer
{

    /**
     * Identify customer after login
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function customerLogin(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('richpanel_analytics');
        $customer = $observer->getEvent()->getCustomer();
        $helper->addEvent('track', 'identify', NULL, NULL, $customer->getId());
    }

    /**
     * Track page views
     *   - homepage & CMS pages
     *   - product view pages
     *   - category view pages
     *   - cart view
     *   - checkout
     *   - any other pages (get title from head)
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function trackPageView(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('richpanel_analytics');
        $action = (string)$observer->getEvent()->getAction()->getFullActionName();

        if ($this->_isRejected($action)) {
            return;
        }
        // Those 2 lines are checking for AJAX events
        // if ($observer->getEvent()->getAction()->getRequest()->isAjax()) {
        //     return;
        // }

        // Catalog search pages
        if ($action == 'catalogsearch_result_index') {
            $query = Mage::helper('catalogsearch')->getQuery();
            if ($text = $query->getQueryText()) {
                $resultCount = Mage::app()->getLayout()->getBlock('search.result')->getResultCount();
                $properties = array(
                    'query' => $text,
                    'result_count' => $resultCount
                );
                $helper->addEvent('track', 'search', $properties);
                return;
            }
        }
        // Fishpigs_Attribute_Splash_Page integration plugin
        if ($action == 'attributeSplash_page_view') {
            $this->_viewSplashPage($helper);
            return;
        }

        // homepage & CMS pages
        if ($action == 'cms_index_index' || $action == 'cms_page_view') {
            $title = Mage::getSingleton('cms/page')->getTitle();
            $data =  array(
                'name'    =>  $title
            );
            $helper->addEvent('track', 'page_view', $data);
            return;
        }
        // category view pages
        if ($action == 'catalog_category_view') {
            $category = Mage::registry('current_category');
            $data =  array(
                'id'    =>  $category->getId(),
                'name'  =>  $category->getName(),
                'parent' => $this->getCategoryParent($category->getId())
            );
            $helper->addEvent('track', 'view_category', $data);
            return;
        }
        // product view pages
        if ($action == 'catalog_product_view') {
            $product = Mage::registry('current_product');
            $data =  array(
                'id'    => $product->getId(),
                'sku'   => $product->getSku(),
                'name'  => $product->getName(),
                'price' => $product->getFinalPrice(),
                'url'   => $product->getProductUrl()
            );
            // Additional information ( image and categories )
            // if ($product->getImage())
            //     $data['image_url'] = (string)Mage::helper('catalog/image')->init($product, 'image');
            if ($product->getImage())
                $data['image_url'] = $this->getProductImages($product->getId());

            if (count($product->getCategoryIds())) {
                $categories = array();
                $collection = $product->getCategoryCollection()->addAttributeToSelect('*');
                foreach ($collection as $category) {
                    $categories[] = array(
                        'id' => $category->getId(),
                        'name' => $category->getName(),
                        'parent' => $this->getCategoryParent($category->getId())
                    );
                }
                $data['categories'] = $categories;
            }
            $helper->addEvent('track', 'view_product', $data);
            return;
        }
        // cart view
        if ($action == 'checkout_cart_index') {
            $helper->addEvent('track', 'view_cart', array());
            return;
        }
        // checkout
        if ($action != 'checkout_cart_index' && strpos($action, 'checkout') !== false && strpos($action, 'success') === false && strpos($action, 'add') === false) {
            $helper->addEvent('track', 'checkout_start', array());
            return;
        }
    }

    public function getCategoryParent($categoryId) {
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $catgoriy_result = array();
        foreach ($category->getParentCategories() as $parent) {
            $catgoriy_result[] = array(
                'id' => $parent->getId(),
                'name' => $parent->getName()
            );
        }
        return $catgoriy_result;
    }

    public function getProductImages($productId)
    {
        $gallery_images = Mage::getModel('catalog/product')->load($productId)->getMediaGalleryImages();

        $items = array();

        foreach($gallery_images as $g_image) {
            $items[] = $g_image['url'];
        }
        return $items;
    }

    /**
     * Event for adding product to cart
     * "checkout_cart_product_add_after"
     *
     * @param Varien_Event_Observer $observer [description]
     */
    public function addToCart(Varien_Event_Observer $observer)
    {
        /**
         * @var Mage_Sales_Model_Quote_Item
         */
        $item = $observer->getQuoteItem();
        $product = $item->getProduct();
        $cartProduct = $observer->getProduct();

        if ($cartProduct->isGrouped()) {
            $options = Mage::app()->getRequest()->getParam('super_group');
            if (is_array($options)) {
                foreach ($options as $productId => $qty) {
                    $this->_addToCart((int)$productId, $cartProduct, (int)$qty);
                }
            }
        } elseif($cartProduct->isConfigurable()) {
            $this->_addToCart($product->getId(), $cartProduct, $item->getQty());
        } else {
            $this->_addToCart($cartProduct->getId(), $cartProduct, $item->getQty());
        }

    }

    /**
     * Event for removing item from shopping bag
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function removeFromCart(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('richpanel_analytics');
        $item = $observer->getQuoteItem();
        $product = $item->getProduct();

        $data = array(
            'id' => $product->getId()
        );

        $helper->addEvent('track', 'remove_from_cart', $data);
    }

    /**
     * Track placing a new order from customer
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function trackNewOrder(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('richpanel_analytics');
        $data = array();
        $order = $observer->getOrder();
        if ($order->getId()) {
            $data = $helper->prepareOrderDetails($order, "new");
            $userProperties = NULL;
            if($order->getCustomerIsGuest()) {
                $userProperties = array(
                    'uid' => $order->getCustomerEmail(),
                    'email' => $order->getCustomerEmail(),
                    'name'          => $order->getCustomerFirstname(). ' '. $order->getCustomerLastname(),
                    'firstName'    => $order->getCustomerFirstname(),
                    'lastName'     => $order->getCustomerLastname(),
                );
                $helper->addEvent('track', 'identify', NULL, $userProperties);
            }
            if ($userProperties) {
                $helper->addEvent('track', 'order', $data, $userProperties);
            } else {
                $helper->addEvent('track', 'order', $data, NULL, $order->getCustomerId());
            }
        }
    }

    /**
     * Track adding discount codes in shopping bag
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function trackCoupon(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('richpanel_analytics');
        $code = Mage::getSingleton('checkout/cart')->getQuote()->getCouponCode();
        if (strlen($code)) {
            $helper->addEvent('track', 'applied_coupon', $code);
        }
    }

    /**
     * Send order information after save
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function updateOrder(Varien_Event_Observer $observer)
    {
        $order = $observer->getOrder();

        $helper = Mage::helper('richpanel_analytics');
        $helper->callBatchApi($order->getStoreId(), array($order), true, 'order_status_update');
        Mage::log("Calling Order Status Update");
    }

    /**
    * Events that we don't want to track
    *
    * @param string event
    */
    private function _isRejected($event)
    {
        return in_array(
            $event,
            array('catalogsearch_advanced_result', 'catalogsearch_advanced_index')
        );
    }

    /**
     * Add to cart event
     *
     * @param integer $productId
     * @param Mage_Catalog_Model_Product  $item
     * @param integer $qty
     */
    private function _addToCart($productId, $item, $qty)
    {
        $helper = Mage::helper('richpanel_analytics');
        $product = Mage::getModel('catalog/product')->load($productId);

        $data =  array(
            'id'            => (int)$product->getId(),
            'sku'           => $item->toArray()['sku'],
            'price'         => (float)$product->getFinalPrice(),
            'name'          => $product->getName(),
            'url'           => $product->getProductUrl(),
            'quantity'      => $qty
        );

        if ($product->getImage()) {
            $data['image_url'] = $this->getProductImages($product->getId());
        }

        if (count($product->getCategoryIds())) {
            $categories = array();
            $collection = $product->getCategoryCollection()->addAttributeToSelect('*');
            foreach ($collection as $category) {
                $categories[] = array(
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'parent' => $this->getCategoryParent($category->getId())
                );
            }
            $data['categories'] = $categories;
        }

        // Add options for grouped or configurable products
        if ($item->isGrouped() || $item->isConfigurable()) {
            $data['id']     = $item->getId();
            $data['name']   = $item->getName();
            $data['url']    = $item->getProductUrl();
            // Options
            $data['option_id'] = $product->getSku();
            $data['option_sku'] = $product->getSku();
            $data['option_name'] = trim(str_replace("-", " ", $product->getName()));
            $data['option_price'] = (float)$product->getFinalPrice();
            $data['option_image_url'] = $this->getProductImages($product->getSku());
        }

        $helper->addEvent('track', 'add_to_cart', $data);
    }

    /**
    * Splash page event
    */
    private function _viewSplashPage($helper)
    {
        $splashPage = Mage::registry('splash_page');

        if (is_null($splashPage) || !$splashPage->canDisplay()) {
            return false;
        }

        $data =  array(
            'id'    =>  'SPL-'.$splashPage->getId(),
            'name'  =>  $splashPage->getName()
        );
        $helper->addEvent('track', 'view_category', $data);
    }
}
