<?
/**
 * Class Sibirix_View_Helper_Block
 */
abstract class Sibirix_View_Helper_Block extends Zend_View_Helper_Abstract {

    protected $_script;

    public function __construct() {
        if (!$this->_script) {
            throw new Zend_Exception('not set view script');
        }

        $this->_initView();
        $this->init();
    }

    public function direct() {
        return $this->__toString();
    }

    public function __toString() {
        return $this->render();
    }

    public function render() {
        return $this->view->render($this->_script);
    }

    private function _initView() {
        if (!$this->view) {
            $this->setView(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'));
        }

        return $this;
    }

    abstract public function init();
}
