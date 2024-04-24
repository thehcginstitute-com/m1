<?php
class MailChimp_BatchOperations extends MailChimp_Abstract
{
	/**
	 * @param $operations
	 * @return mixed
	 * @throws MailChimp_Error
	 * @throws MailChimp_HttpError
	 */
	function add($operations)
	{
		return $this->_master->call('batches', $operations, Ebizmarts_MailChimp::POST, false);
	}

	/**
	 * @param $fields           A comma-separated list of fields to return. Reference parameters of sub-objects
	 *                          with dot notation.
	 * @param $excludeFields    A comma-separated list of fields to exclude. Reference parameters of sub-objects
	 *                          with dot notation.
	 * @return mixed
	 * @throws MailChimp_Error
	 * @throws MailChimp_HttpError
	 */
	function status(string $id, $fields = null, $excludeFields = null)
	{
		$_params = array();

		if ($fields) {
			$_params['fields'] = $fields;
		}

		if ($excludeFields) {
			$_params['exclude_fields'] = $excludeFields;
		}

		return $this->_master->call('batches/' . $id, $_params, Ebizmarts_MailChimp::GET);
	}

	/**
	 * @param $fields           A comma-separated list of fields to return. Reference parameters of sub-objects
	 *                              with dot notation.
	 * @param $excludeFields    A comma-separated list of fields to exclude. Reference parameters of sub-objects with
	 *                              dot notation.
	 * @param $count            The number of records to return.
	 * @param $offset           The number of records from a collection to skip. Iterating over large collections with
	 *                          this parameter can be slow.
	 * @return mixed
	 * @throws MailChimp_Error
	 * @throws MailChimp_HttpError
	 */
	function getAll($fields = null, $excludeFields = null, $count = null, $offset = null)
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

		return $this->_master->call('batches', $_params, Ebizmarts_MailChimp::GET);
	}
}
