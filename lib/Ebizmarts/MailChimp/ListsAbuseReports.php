<?php
/**
 * mailchimp-lib Magento Component
 *
 * @category  Ebizmarts
 * @package   mailchimp-lib
 * @author    Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date:     5/2/16 3:42 PM
 * @file:     AbuseReports.php
 */
class MailChimp_ListsAbuseReports extends MailChimp_Abstract
{
    /**
     * @param $listId               The unique id for the list.
     * @param null $fields          A comma-separated list of fields to return. Reference parameters of sub-objects
     *                                       with dot notation.
     * @param null $excludeFields   A comma-separated list of fields to exclude. Reference parameters of sub-objects
     *                                       with dot notation.
     * @param null $count           The number of records to return.
     * @param null $offset          The number of records from a collection to skip. Iterating over large collections
     *                                       with this parameter can be slow.
     * @return mixed
     * @throws MailChimp_Error
     * @throws MailChimp_HttpError
     */
    function getAll($listId, $fields = null, $excludeFields = null, $count = null, $offset = null)
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

        return $this->_master->call('lists/' . $listId . '/abuse_reports', $_params, Ebizmarts_MailChimp::GET);
    }

    /**
     * @param $listId               The unique id for the list.
     * @param $reportId             The id for the abuse report.
     * @param null $fields          A comma-separated list of fields to return. Reference parameters of sub-objects
     *                                        with dot notation.
     * @param null $excludeFields   A comma-separated list of fields to exclude. Reference parameters of sub-objects
     *                                        with dot notation.
     * @param null $count           The number of records to return.
     * @param null $offset          The number of records from a collection to skip. Iterating over large collections
     *                                        with this parameter can be slow.
     * @return mixed
     * @throws MailChimp_Error
     * @throws MailChimp_HttpError
     */
    function get($listId, $reportId, $fields = null, $excludeFields = null, $count = null, $offset = null)
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

        return $this->_master->call(
            'lists/' . $listId . '/abuse_reports/' . $reportId, $_params, Ebizmarts_MailChimp::GET
        );
    }
}
