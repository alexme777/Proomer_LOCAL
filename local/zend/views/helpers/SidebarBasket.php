<?

/**
 * Class Zend_View_Helper_SidebarBasket
 */
class Zend_View_Helper_SidebarBasket extends Zend_View_Helper_Abstract {

    public function sidebarBasket() {
        $basketModel = new Sibirix_Model_Basket();
        $basketData = $basketModel->getBasket();
			
        return $this->view->partial('_partials/sidebar-basket.phtml', array(
            "basketItems" => $basketData["basketItems"],
            "basketTotal" => $basketData["basketTotal"]
        ));
    }
}