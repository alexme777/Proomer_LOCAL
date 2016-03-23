<?

/**
 * Class Zend_View_Helper_Order
 */
class Zend_View_Helper_Order extends Zend_View_Helper_Abstract {

    public function order() {
        $basketModel = new Sibirix_Model_Basket();
        $basketData = $basketModel->getBasket();
        return $this->view->partial('_partials/order.phtml', array(
            "basketItems" => $basketData["basketItems"],
            "basketTotal" => $basketData["basketTotal"]
        ));
    }
}