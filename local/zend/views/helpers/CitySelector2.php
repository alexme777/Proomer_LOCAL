<?

/**
 * Class Zend_View_Helper_CitySelector
 */
class Zend_View_Helper_CitySelector2 extends Zend_View_Helper_Abstract {

    public function citySelector2() {
        $cityModel    = new Sibirix_Model_City();
        $cityList     = $cityModel->getElements();
        $userLocation = Sibirix_Model_User::getUserLocation();

        return $this->view->partial('_partials/header/city-selector2.phtml', array("cityList" => $cityList, "userLocation" => $userLocation));
    }
}
