<?

/**
 * Class Zend_View_Helper_ElementCntList
 */
class Zend_View_Helper_Sorting extends Zend_View_Helper_Abstract {

    public function sorting($sortType) {
        return $this->view->partial('_partials/sorting/' . $sortType . '-sorting.phtml');
    }
}