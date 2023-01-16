<?php

/**
 * Class IWD_SettlementReport_Model_Authorize_Response
 */
class IWD_SettlementReport_Model_Authorize_Response
{
    /**
     * @var SimpleXMLElement
     */
    public $xml;

    /**
     * IWD_SettlementReport_Model_Authorize_Response constructor.
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = $response;
        if ($response) {
            $this->xml = @simplexml_load_string($this->removeResponseXMLNS($response));
        }
    }

    /**
     * @param $elementName
     * @return bool|string
     */
    public function getElementContents($elementName)
    {
        $start = "<$elementName>";
        $end = "</$elementName>";
        if (strpos($this->response, $start) === false || strpos($this->response, $end) === false) {
            return false;
        } else {
            $startPosition = strpos($this->response, $start) + strlen($start);
            $endPosition = strpos($this->response, $end);
            return substr($this->response, $startPosition, $endPosition - $startPosition);
        }
    }

    /**
     * @param $input
     * @return mixed
     */
    protected function removeResponseXMLNS($input)
    {
        $input = str_replace(' xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd"', '', $input);
        $input = str_replace(' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"', '', $input);
        return str_replace(' xmlns:xsd="http://www.w3.org/2001/XMLSchema"', '', $input);
    }
}
