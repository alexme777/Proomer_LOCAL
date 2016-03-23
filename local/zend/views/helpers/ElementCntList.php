<?

/**
 * Class Zend_View_Helper_ElementCntList
 */
class Zend_View_Helper_ElementCntList extends Zend_View_Helper_Abstract {

    public function elementCntList($profile = false) {
        return $this->view->partial('_partials/element-cnt-list.phtml', array(
            "elementCnt"   => Sibirix_Model_ViewCounter::getCounterList($profile),
            "currentValue" => Sibirix_Model_ViewCounter::getViewCounter($profile)
        ));
    }
}