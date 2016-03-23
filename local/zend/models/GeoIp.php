<?php
/**
 * @property Zend_Http_Client $httpClient
 * @property Zend_Session_Namespace $session
 */

class Sibirix_Model_GeoIp {

    const COOKIE_NAME = 'CITY_ID';
    const CONFIRMED   = 'CONFIRMED';

    /**
     *
     */
    const SERVICE_URI = 'http://ipgeobase.ru:7020/geo';
    /**
     * @var
     */
    protected $httpClient;

    /**
     * @var array
     */
    protected $ipParams = array(
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_REAL_IP',
        'REMOTE_ADDR'
    );

    /**
     * @var CMain
     */
    protected $_application;

    /**
     * @var Sibirix_Model_City
     */
    protected $_location;

    /**
     * @var
     */
    protected $session;

    /**
     * @var array
     */
    protected $serverParams = array();

    /**
     * @param array $serverParams
     */
    public function __construct($serverParams = array()){
        if (!($serverParams)) {
            $serverParams = Zend_Controller_Front::getInstance()->getRequest()->getServer();
        }
        $this->setServerParams($serverParams);
        $this->init();
    }

    /**
     * Init
     */
    public function init() {
        $this->httpClient = new Zend_Http_Client(self::SERVICE_URI);
        $this->httpClient->setConfig(array('timeout' => 5));
        $this->_application = Zend_Registry::get("BX_APPLICATION");
        $this->_location    = new Sibirix_Model_City();
        CModule::IncludeModule("iblock");
    }

    /**
     * @param $serverParams
     * @return $this
     */
    public function setServerParams($serverParams){
        if (is_array($serverParams)) {
            $this->serverParams = $serverParams;
        }
        return $this;
    }

    /**
     * Определяем местоположения по ip
     * @return int
     */
    public function getClientLocation() {

        // Ищем город по IP
        foreach ($this->ipParams as $paramName) {
            if (!empty($this->serverParams[$paramName])) {

                $ip = $this->serverParams[$paramName];
//                $ip = '62.78.80.112';  // - Барнаул
//                $ip = '109.171.55.112';  // - Кемерво
//                $ip = '91.199.232.112';  // - Новосибирск

                try {
                    $response = $this->httpClient->setParameterGet('ip', $ip)->request(Zend_Http_Client::GET);

                } catch (Zend_Exception $e) {
                    return false;
                }

                if ($response->isSuccessful()) {
                    $xmlObj = simplexml_load_string($response->getBody());

                    if (!isset($xmlObj->ip)) {
                        continue;
                    }

                    if ($xmlObj->ip->city) return reset($xmlObj->ip->city);
                    if ($xmlObj->ip->region) return reset($xmlObj->ip->region);
                    if ($xmlObj->ip->country) return reset($xmlObj->ip->country);

                    break;
                }
            }
        }

        return false;
    }

    public function getClientBitrixLocation() {
        $userCityName = $this->getClientLocation();
        $cityId = $this->_location->getCityIdByName($userCityName);
        return $cityId;
    }

}