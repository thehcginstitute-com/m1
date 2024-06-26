<?php
/**
 * mailchimp-lib Magento Component
 *
 * @category  Ebizmarts
 * @package   mailchimp-lib
 * @author    Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date:     4/29/16 4:32 PM
 * @file:     EcommerceOrdersLines.php
 */
class MailChimp_EcommerceOrdersLines extends MailChimp_Abstract
{
	/**
	 * @param $storeId              The store id.
	 * @param $orderId              The id for the order in a store.
	 * @param $id                   A unique identifier for the order line item.
	 * @param $productId
	 * @param $productVariantId
	 * @param $quantity             The quantity of an order line item.
	 * @param $price                The price of an order line item.
	 * @return mixed
	 * @throws MailChimp_Error
	 * @throws MailChimp_HttpError
	 */
	function add($storeId, $orderId, $id, $productId, $productVariantId, $quantity, $price) {return
		$this->_master->call('/ecommerce/stores/' . $storeId . '/orders/' . $orderId . '/lines', [
			'id'=> $id
			# 2024-04-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# 1) "`Ebizmarts_MailChimp`: «lines.item:0.product_id :
			# Schema describes string, integer found instead»":
			# https://github.com/thehcginstitute-com/m1/issues/584
			# 2) https://mailchimp.com/developer/marketing/api
			,'product_id'=> (string)$productId
			# 2024-04-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# 1) "`Ebizmarts_MailChimp`: «lines.item:0.product_variant_id :
			# Schema describes string, integer found instead»":
			# https://github.com/thehcginstitute-com/m1/issues/585
			# 2) https://mailchimp.com/developer/marketing/api
			,'product_variant_id'=> (string)$productVariantId
			,'quantity' => $quantity
			,'price' => $price
		], Ebizmarts_MailChimp::POST)
	;}

	/**
	 * @param       $storeId        The store id.
	 * @param       $orderId        The id for the order in a store.
	 * @param null  $fields         A comma-separated list of fields to return. Reference parameters of sub-objects
	 *                                  with dot notation.
	 * @param null  $excludeFields  A comma-separated list of fields to exclude. Reference parameters of sub-objects
	 *                                  with dot notation.
	 * @param null  $count          The number of records to return.
	 * @param null  $offset         The number of records from a collection to skip. Iterating over large collections
	 *                                  with this parameter can be slow.
	 * @return mixed
	 * @throws MailChimp_Error
	 * @throws MailChimp_HttpError
	 */
	function getAll($storeId, $orderId, $fields = null, $excludeFields = null, $count = null, $offset = null)
	{
		$_params = array();
		if ($fields) {
			$_params['fields'] = $fields;
		}

		if ($excludeFields) {
			$_params['exclude_fields'] = $excludeFields;
		}

		if ($count) {
			$_params['count'] = $count;
		}

		if ($offset) {
			$_params['offset'] = $offset;
		}

		$url = '/ecommerce/stores/' . $storeId . '/orders/' . $orderId . '/lines';

		return $this->_master->call($url, $_params, Ebizmarts_MailChimp::GET);
	}

	/**
	 * @param       $storeId        The store id.
	 * @param       $orderId        The id for the order in a store.
	 * @param       $lineId         The id for the line item of an order.
	 * @param null  $fields         A comma-separated list of fields to return. Reference parameters of sub-objects
	 *                                  with dot notation.
	 * @param null  $excludeFields  A comma-separated list of fields to exclude. Reference parameters of sub-objects
	 *                                  with dot notation.
	 * @return mixed
	 * @throws MailChimp_Error
	 * @throws MailChimp_HttpError
	 */
	function get($storeId, $orderId, $lineId, $fields = null, $excludeFields = null)
	{
		$_params = array();

		if ($fields) {
			$_params['fields'] = $fields;
		}

		if ($excludeFields) {
			$_params['exclude_fields'] = $excludeFields;
		}

		$url = '/ecommerce/stores/' . $storeId . '/orders/' . $orderId . '/lines/' . $lineId;

		return $this->_master->call($url, $_params, Ebizmarts_MailChimp::GET);
	}

	/**
	 * @param       $storeId            The store id.
	 * @param       $orderId            The id for the order in a store.
	 * @param       $lineId             The id for the line item of an order.
	 * @param null  $productId          The unique identifier for the product associated with the order line item.
	 * @param null  $productVariantId   A unique identifier for the product variant associated with the order line item.
	 * @param null  $quantity           The quantity of an order line item.
	 * @param null  $price              The price of an order line item.
	 * @return mixed
	 * @throws MailChimp_Error
	 * @throws MailChimp_HttpError
	 */
	function modify(
		$storeId,
		$orderId,
		$lineId,
		$productId = null,
		$productVariantId = null,
		$quantity = null,
		$price = null
	) {
		$_params = [];
		if ($productId) {
			# 2024-04-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# 1) "`Ebizmarts_MailChimp`: «lines.item:0.product_id :
			# Schema describes string, integer found instead»":
			# https://github.com/thehcginstitute-com/m1/issues/584
			# 2) https://mailchimp.com/developer/marketing/api
			$_params['product_id'] = (string)$productId;
		}
		if ($productVariantId) {
			# 2024-04-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# 1) "`Ebizmarts_MailChimp`: «lines.item:0.product_variant_id :
			# Schema describes string, integer found instead»":
			# https://github.com/thehcginstitute-com/m1/issues/585
			# 2) https://mailchimp.com/developer/marketing/api
			$_params['product_variant_id'] = (string)$productVariantId;
		}
		if ($quantity) {
			$_params['quantity'] = $quantity;
		}
		if ($price) {
			$_params['price'] = $price;
		}
		$url = '/ecommerce/stores/' . $storeId . '/orders/' . $orderId . '/lines/' . $lineId;
		return $this->_master->call($url, $_params, Ebizmarts_MailChimp::PATCH);
	}

	/**
	 * @param $storeId                  The store id.
	 * @param $orderId                  The id for the order in a store.
	 * @param $lineId                   The id for the line item of an order.
	 * @return mixed
	 * @throws MailChimp_Error
	 * @throws MailChimp_HttpError
	 */
	function delete($storeId, $orderId, $lineId)
	{
		$url = '/ecommerce/stores/' . $storeId . '/orders/' . $orderId . '/lines/' . $lineId;

		return $this->_master->call($url, null, Ebizmarts_MailChimp::DELETE);
	}
}
