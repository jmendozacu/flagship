<?php
class Angelleye_PaypalBanner_Model_Client extends Mage_Core_Model_Abstract
{
    /**
     * API endpoint
     * @var string
     */
    protected $_endpoint = '';

    /**
     * API BN Code
     * @var string
     */
    protected $_bnCode = '';

    /**
     * API Access Key
     * @var string
     */
    protected $_accessKey = '';

    /**
     * API Secret
     * @var string
     */
    protected $_secret = '';

    /**
     * Current Timestamp
     * @var string
     */
    protected $_timestamp = '';

    /**
     * API Token
     * @var string
     */
    protected $_token = '';

    /**
     * API call results
     * @var object|null
     */
    protected $_responseData  = null;

    /**
     * Payer Name
     * @var string
     */
    protected $_name  = null;

    /**
     * Payer Email
     * @var string
     */
    protected $_email  = null;

    /**
     * Check whether to use sandbox settings
     * @var bool
     */
    protected $_sandboxMode = false;

    /**
     * Constructor
     */
    public function __construct($params)
    {
        list($name, $email)=$params;
        $this->_name = $name;
        $this->_email = $email;
        parent::__construct();
    }

    /**
     * Internal Constructor. Initialize API Client
     */
    protected function _construct()
    {
        date_default_timezone_set('America/Chicago');
        $mode = $this->_sandboxMode ? '-sandbox':'';

        $this->_endpoint = Mage::getStoreConfig('paypalbanner'.$mode.'/settings/api_endpoint');
        $this->_bnCode = Mage::getStoreConfig('paypalbanner'.$mode.'/settings/api_bn_code');
        $this->_accessKey = Mage::getStoreConfig('paypalbanner'.$mode.'/settings/api_access_key');
        $this->_secret = Mage::getStoreConfig('paypalbanner'.$mode.'/settings/api_secret');
        $this->_timestamp = round(microtime(true) * 1000);
        $this->_token = $token = sha1($this->_secret.$this->_timestamp);
        $this->_responseData = new Varien_Object();
    }

    /**
     * Get headers for API call
     * @return array
     */
    protected function _getHeaders()
    {
        return  array(
            "AUTHORIZATION: FPA ".$this->_accessKey.":".sha1($this->_secret.$this->_timestamp).":".$this->_timestamp,
            "CONTENT-TYPE: application/json",
            "ACCEPT: application/json"
        );
    }

    /**
     * Get params for API call
     * @return array
     */
    protected function _getParams()
    {
        return  array(
            'payerId' => '',
            'sellerName' => $this->_name,
            'emailAddress' => $this->_email,
            'bnCode' => $this->_bnCode
        );
    }

    /**
     * Get params for API proxy call
     * @return array
     */
    protected function _getProxyParams()
    {
        return '?seller_name='.urlencode($this->_name).'&email_address='.urlencode($this->_email);
    }

    /**
     * Get API response object
     * @return object|null
     */
    protected function _getResponse()
    {
        return $this->_responseData;
    }


    /**
     * API Call.
     * @return array
     */
    public function call()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $this->_endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->_getParams()));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->_getHeaders());

        $this->_responseData = json_decode(curl_exec($curl));
        return $this;
    }


    /**
     * API Call proxy server.
     * @return array
     */
    public function callProxy()
    {
        $mode = $this->_sandboxMode ? '-sandbox':'';
        $endpoint = Mage::getStoreConfig('paypalbanner'.$mode.'/settings/api_proxy_endpoint');
        $endpoint .= $this->_getProxyParams();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $this->_responseData = json_decode(curl_exec($curl));
        curl_close($curl);
        return $this;
    }

    /**
     * Get publisher ID from API response
     * @return string|null
     */
    public function extractPublisherId()
    {

        if ($this->_getResponse()){
            if (!empty($this->_getResponse()->publisher_id)) {
                return $this->_getResponse()->publisher_id;
            } else {
                return $this->_getResponse()->publisherId;
            }
        }
        return null;
    }

}
